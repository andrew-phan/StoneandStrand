<?php

namespace Ss\Theme\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Generate url rewrite Designer
 */
class MigrateUrlCommand extends Command
{
    const INPUT_PARAMETER_FILE = 'file';
    const INPUT_PARAMETER_FORCE_301 = 'force header 301';

    protected $resultPageFactory;
    protected $_resourceModel;
    protected $_urlFinderInterface;
    protected $_urlRewrite;

    /**
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinderInterface
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinderInterface,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewrite
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_urlFinderInterface = $urlFinderInterface;
        $this->_urlRewrite = $urlRewrite;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('ss_theme:migrate_url')
            ->setDescription('Migrate url.')
            ->addArgument(static::INPUT_PARAMETER_FILE, InputArgument::REQUIRED, 'file import.')
            ->addArgument(static::INPUT_PARAMETER_FORCE_301, InputArgument::OPTIONAL, 'force header 301');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument(static::INPUT_PARAMETER_FILE);
        $force301 = $input->getArgument(static::INPUT_PARAMETER_FORCE_301);

        if (!file_exists($filePath)) {
            $output->writeln("file $filePath does not exist, exiting.");
            return;
        }

        $file = fopen($filePath, 'r');
        if (!$file) {
            $output->writeln("file $filePath could not be opened for reading, check permissions.  Exiting.\n");
            return;
        }

        if (isset($force301) && !in_array($force301, array(0, 1))) {
            $output->writeln(static::INPUT_PARAMETER_FORCE_301 . ": $force301 is invalid. It should be 0 or 1\n");
            return;
        }

        // necessary if a large csv file
        set_time_limit(0);

        $totalRow = count(file($filePath));
        $row = 1;

        while (($data = fgetcsv($file, 1000, ',')) !== FALSE) {
            // number of fields in the csv
            $oldUrl = $data[0];
            $newUrl = $data[1];

            if ($oldUrl[strlen($oldUrl) - 1] == '/') {
                $oldUrl = substr($oldUrl, 0, -1);
            }

            if ($newUrl[strlen($newUrl) - 1] == '/') {
                $newUrl = substr($newUrl, 0, -1);
            }

            $output->write("$row/$totalRow ");

            try {
                $select = [
                    'request_path' => $oldUrl,
                ];
                $checkExist = $this->_urlFinderInterface->findOneByData($select);
                $urlRewrite = $this->_urlRewrite->create();

                if ($checkExist) {
                    $urlRewrite->load($checkExist->getUrlRewriteId());
                    if ($force301) {
                        $urlRewrite->setRedirectType(301);
                    }

                    $output->write("Update, ");
                } else {
                    $urlRewrite->setRedirectType(301);
                    $urlRewrite->setStoreId(1);
                    $urlRewrite->setEntityType('custom');
                    $urlRewrite->setRequestPath($oldUrl);

                    $output->write("Insert, ");
                }

                $urlRewrite->setTargetPath($newUrl);
                $urlRewrite->save();

                $redirectType = $urlRewrite->getRedirectType();
                $output->writeln("Success, $redirectType.");
            } catch (Exception $ex) {
                $output->writeln("Error!!!");
            }

            $row++;
        }
        fclose($file);

        $output->writeln("Done!!!");
        return;
    }

}