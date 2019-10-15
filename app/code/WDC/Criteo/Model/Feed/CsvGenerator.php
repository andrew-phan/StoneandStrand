<?php

namespace WDC\Criteo\Model\Feed;

use Magento\Framework\Exception\InputException;

class CsvGenerator
    extends AbstractGenerator
{
    public function generate($destination)
    {
        $file = fopen($destination, 'w');
        if (!$file) {
            throw new InputException("Could not open $destination for writing.");
        }
        $data = $this->_toArray();
        foreach ($data as $i => $row) {
            if ($i == 0) {
                fputcsv($file, array_keys($row));
            }
            fputcsv($file, $row);
        }
        fclose($file);
    }
}