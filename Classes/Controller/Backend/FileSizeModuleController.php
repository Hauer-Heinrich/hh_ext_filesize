<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtFilesize\Controller\Backend;

use \Psr\Http\Message\ResponseInterface;

// use \TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use \TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Backend\Template\ModuleTemplate;
use \TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use \TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Page\PageRenderer;
use \TYPO3\CMS\Core\Imaging\Icon;
use \TYPO3\CMS\Core\Imaging\IconFactory;
use \TYPO3\CMS\Core\Resource\File;
use \TYPO3\CMS\Core\Resource\ResourceFactory;
use \TYPO3\CMS\Core\Resource\StorageRepository;


#[AsController]
final class FileSizeModuleController extends ActionController implements \TYPO3\CMS\Extbase\Mvc\Controller\ControllerInterface {

    protected ?ModuleTemplate $moduleTemplate = null;
    protected string $fileExtensions = 'jpg|jpeg|png';

    /**
     * maxFileSize in KB
     *
     * @var integer
     */
    protected int $maxFileSize = 400;

    public function __construct(
        protected readonly ModuleTemplateFactory $moduleTemplateFactory,
        protected BackendUriBuilder $backendUriBuilder,
        protected PageRenderer $pageRenderer,
        protected IconFactory $iconFactory,
        private readonly ResourceFactory $resourceFactory,
        private readonly StorageRepository $storageRepository,
    ) {}

    /**
     * Init module state.
     * This isn't done within __construct() since the controller
     * object is only created once in extbase when multiple actions are called in
     * one call. When those change module state, the second action would see old state.
     */
    public function initializeAction(): void
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setTitle(LocalizationUtility::translate('LLL:EXT:hh_ext_filesize/Resources/Private/Language/locallang_mod.xlf:mlang_tabs_tab'));
    }

    public function fileSizesAction(): ResponseInterface {
        $listUrlParams = [];
        if($this->request->getArguments() != NULL) {
            if($this->request->hasArgument('fileExtensions') && !empty($this->request->getArgument('fileExtensions'))) {
                $this->fileExtensions = $this->request->getArgument('fileExtensions');
                $listUrlParams['tx_hhextfilesize_file_hhextfilesizetxhhextfilesize[fileExtensions]'] = $this->fileExtensions;
            }

            if($this->request->hasArgument('maxFileSize') && !empty($this->request->getArgument('maxFileSize'))) {
                $this->maxFileSize = intval($this->request->getArgument('maxFileSize'));
                $listUrlParams['tx_hhextfilesize_file_hhextfilesizetxhhextfilesize[maxFileSize]'] = $this->maxFileSize;
            }
        }

        $fileSizes = [];

        try {
            $selectedDirectory = \explode(':', GeneralUtility::_GET('id')); // PageTree - FileTree
            $publicDirectory = rtrim($this->storageRepository->findByCombinedIdentifier($selectedDirectory[0].':')->getStorageRecord()['configuration']['basePath'], '/') .$selectedDirectory[1];
        } catch (\Throwable $th) {
            $publicDirectory = 'fileadmin';
        }

        $files = iterator_to_array($this->filesIn(\TYPO3\CMS\Core\Core\Environment::getPublicPath().'/'.$publicDirectory));
        $maxFileSizeBytes = $this->maxFileSize * 1000;
        $listUrl = $this->listURL($listUrlParams); // needed for return URL

        if(!empty($files)) {
            foreach($files as $key => $value) {
                if($value->getSize() > $maxFileSizeBytes) {
                    $filePaths = \explode($publicDirectory, GeneralUtility::fixWindowsFilePath($key));

                    if(isset($filePaths[1])) {
                        $file = $this->resourceFactory->getFileObjectFromCombinedIdentifier($publicDirectory.$filePaths[1]);
                        $currentFileSize = filesize($key);
                        $arrKey = '/'.$publicDirectory.$filePaths[1];
                        $fileSizes[$arrKey]['size'] = $this->human_filesize($currentFileSize);
                        $fileSizes[$arrKey]['sizeBytes'] = $currentFileSize;

                        // show replace Button / Link
                        if ($file instanceof File && $file->checkActionPermission('replace')) {
                            $fullIdentifier = $file->getCombinedIdentifier();

                            $attributes = [
                                'href' => (string)$this->backendUriBuilder->buildUriFromRoute('file_replace', ['target' => $fullIdentifier, 'uid' => $file->getUid(), 'returnUrl' => $listUrl]),
                                'title' => LocalizationUtility::translate('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:cm.replace'),
                            ];
                            $fileSizes[$arrKey]['replace'] = '<a class="btn btn-default" ' . GeneralUtility::implodeAttributes($attributes, true) . '>' . $this->iconFactory->getIcon('actions-edit-replace', Icon::SIZE_SMALL)->render() . '</a>';
                        }
                    }
                }
            }
        }

        $this->view->assignMultiple([
            'form' => [
                'fileExtensions' => $this->fileExtensions,
                'maxFileSize' => $this->maxFileSize,
            ],
            'fileSizes' => $fileSizes
        ]);

        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    public function filesIn(string $path): \Generator {
        if (!is_dir($path)) {
            throw new \RuntimeException("{$path} is not a directory ");
        }

        $directoryIterator = new \RecursiveDirectoryIterator($path);
        $iteratorIterator = new \RecursiveIteratorIterator($directoryIterator);
        $it = new \RegexIterator($iteratorIterator, '/\.'.$this->fileExtensions.'$/', \RegexIterator::MATCH);

        yield from $it;
    }

    public function human_filesize(int $bytes, int $decimals = 2): string {
        $factor = floor((strlen((string)$bytes) - 1) / 3);
        if ($factor > 0) $sz = 'KMGT';
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor - 1] . 'B';
    }

    /**
     * Returns list URL; This is the URL of the current script with id and imagemode parameters, that's all.
     *
     * @return string URL
     */
    public function listURL(array $params = []): string {
        $params = array_replace_recursive([
            'pointer' => 'tx_hhextfilesize',
            'id' => 1,
            'tx_hhextfilesize_file_hhextfilesizetxhhextfilesize[action]' => 'fileSizes',
            'tx_hhextfilesize_file_hhextfilesizetxhhextfilesize[controller]' => 'Backend\FileSizeModule',
        ], $params);

        $params = array_filter($params);

        return (string)$this->backendUriBuilder->buildUriFromRoute('file_HhExtFilesizeTxHhextfilesize', $params);
    }
}
