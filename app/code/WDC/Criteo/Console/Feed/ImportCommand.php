<?php

namespace WDC\Criteo\Console\Feed;

use Symfony\Component\Console\Command\Command;
use Magento\Framework\App\State;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand
    extends Command
{
    protected $_state;
    protected $_productCollectionFactory;
    
    public function __construct(
        CollectionFactory $productCollectionFactory,
        State $state,
        $name = null
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_state = $state;
        parent::__construct($name);
    }
    
    protected function configure()
    {
        $this->setName('wdc:criteo:feed:import')
            ->setDescription("Imports feed data into product records")
            ->addArgument(
                'filename',
                InputArgument::REQUIRED,
                'What is the filename to import?'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_state->setAreaCode('adminhtml');
        $filename = $input->getArgument('filename');
        if (!file_exists($filename)) {
            $output->writeln("File $filename does not exist.");
            return;
        }
        $file = fopen($filename, 'r');
        if (!$file) {
            $output->writeln("Could not open $filename for reading.");
            return;
        }
        $keys = fgetcsv($file);
        foreach ($keys as $i => $key) {
            $keys[$i] = trim($key);
        }
        $productData = [];
        while ($values = fgetcsv($file)) {
            $row = array_combine($keys, $values);
            $productData[$row['CRITEO ID']] = [
                'is_in_criteo_feed' => 1,
                'google_id' => $row['GOOGLE ID'],
                'gtin' => $row['GTIN'],
                'mpn' => $row['mpn'],
                'custom_label0' => $row['custom_label_0'],
                'promotion_id' => $row['promotion_id']
            ];
        }
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $products */
        $products = $this->_productCollectionFactory->create();
        $products->addAttributeToSelect(['is_in_criteo_feed', 'google_id', 'gtin', 'mpn', 'custom_labvel0', 'promotion_id'])
            ->setPageSize(100);
        $sz = $products->getLastPageNumber();
        for ($i = 1; $i <= $sz; ++$i) {
            $products->setCurPage($i)->load();
            foreach ($products as $product) {
                /** @var \Magento\Catalog\Model\Product $product */
                if (isset($productData[$product->getId()])) {
                    $data = &$productData[$product->getId()];
                    $product->setData($data);
                    foreach (array_keys($data) as $key) {
                        $product->getResource()->saveAttribute($product, $key);
                    }
                    $output->writeln("Added product " . $product->getSku() . " to criteo feed.");
                } else {
                    $product->setData('is_in_criteo_feed', 0);
                    $product->getResource()->saveAttribute($product, 'is_in_criteo_feed');
                    $output->writeln("Removed product " . $product->getSku() . " from criteo feed.");
                }
            }
            $products->clear();
        }
    }
}