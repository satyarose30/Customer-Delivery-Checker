<?php

declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Console\Command;

use Domus\CustomerDeliveryChecker\Model\ResourceModel\Pincode\CollectionFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('domus:pincode:export')
            ->setDescription('Export pincodes to a CSV file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the output CSV file')
            ->addOption('country', 'c', InputOption::VALUE_OPTIONAL, 'Filter by country code')
            ->addOption('active-only', null, InputOption::VALUE_NONE, 'Export only active pincodes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');
        $countryFilter = $input->getOption('country');
        $activeOnly = (bool)$input->getOption('active-only');

        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $handle = fopen($filePath, 'w');
        if (!$handle) {
            $output->writeln('<error>Unable to create file: ' . $filePath . '</error>');
            return Command::FAILURE;
        }

        $headers = [
            'pincode', 'country_id', 'city', 'state', 'area_name', 'latitude', 'longitude',
            'is_deliverable', 'is_cod_available', 'estimated_delivery_days',
            'shipping_charge', 'cod_charge', 'weight_from', 'weight_to', 'price_from', 'price_to',
            'store_id', 'customer_group_id', 'priority', 'is_active', 'categories', 'products'
        ];
        fputcsv($handle, $headers);

        $collection = $this->collectionFactory->create();

        if ($activeOnly) {
            $collection->addActiveFilter();
        }

        if ($countryFilter) {
            $collection->addCountryFilter($countryFilter);
        }

        $count = 0;
        foreach ($collection as $item) {
            fputcsv($handle, [
                $item->getPincode(),
                $item->getCountryId(),
                $item->getCity(),
                $item->getState(),
                $item->getAreaName(),
                $item->getLatitude(),
                $item->getLongitude(),
                $item->getIsDeliverable() ? 1 : 0,
                $item->getIsCodAvailable() ? 1 : 0,
                $item->getEstimatedDeliveryDays(),
                $item->getShippingCharge(),
                $item->getCodCharge(),
                $item->getWeightFrom(),
                $item->getWeightTo(),
                $item->getPriceFrom(),
                $item->getPriceTo(),
                $item->getStoreId(),
                $item->getCustomerGroupId(),
                $item->getPriority(),
                $item->getIsActive() ? 1 : 0,
                implode(',', $item->getCategories()),
                implode(',', $item->getProducts()),
            ]);
            $count++;
        }

        fclose($handle);

        $output->writeln('<info>Exported ' . $count . ' pincodes to ' . $filePath . '</info>');
        return Command::SUCCESS;
    }
}
