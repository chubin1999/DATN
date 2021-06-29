<?php


namespace Margifox\EducationPortal\Ui\Component\Listing\Column;


class Status extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if ($item) {
                    $item['status'] = $item['status'] ? 'Enabled' : 'Disabled' ;
                }
            }
        }

        return $dataSource;
    }

}
