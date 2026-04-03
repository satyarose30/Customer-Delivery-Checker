<?php
declare(strict_types=1);

namespace Domus\CustomerDeliveryChecker\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Exception\LocalizedException;

class AddIndiaPincodes implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $this->moduleDataSetup->startSetup();

        try {
            $table = $this->moduleDataSetup->getTable('domus_delivery_pincode');

            foreach ($this->getIndiaPincodeData() as $row) {
                $connection->insertOnDuplicate($table, $row);
            }

        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Error inserting pincode data: %1', $e->getMessage())
            );
        }

        $this->moduleDataSetup->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * Get India pincode data
     *
     * @return array
     */
    private function getIndiaPincodeData(): array
    {
        // Major city pincodes for all Indian states and union territories
        return [
            // Andhra Pradesh
            ['pincode' => '500001', 'city' => 'Hyderabad', 'state' => 'Andhra Pradesh', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '530001', 'city' => 'Visakhapatnam', 'state' => 'Andhra Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '517501', 'city' => 'Tirupati', 'state' => 'Andhra Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Arunachal Pradesh
            ['pincode' => '791001', 'city' => 'Itanagar', 'state' => 'Arunachal Pradesh', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            
            // Assam
            ['pincode' => '781001', 'city' => 'Guwahati', 'state' => 'Assam', 'country' => 'IN', 'estimated_days' => 4, 'cod_available' => 1, 'is_active' => 1],
            
            // Bihar
            ['pincode' => '800001', 'city' => 'Patna', 'state' => 'Bihar', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Chandigarh
            ['pincode' => '160001', 'city' => 'Chandigarh', 'state' => 'Chandigarh', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            
            // Chhattisgarh
            ['pincode' => '492001', 'city' => 'Raipur', 'state' => 'Chhattisgarh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Dadra and Nagar Haveli
            ['pincode' => '396001', 'city' => 'Silvassa', 'state' => 'Dadra and Nagar Haveli', 'country' => 'IN', 'estimated_days' => 4, 'cod_available' => 1, 'is_active' => 1],
            
            // Daman and Diu
            ['pincode' => '396210', 'city' => 'Daman', 'state' => 'Daman and Diu', 'country' => 'IN', 'estimated_days' => 4, 'cod_available' => 1, 'is_active' => 1],
            
            // Delhi
            ['pincode' => '110001', 'city' => 'New Delhi', 'state' => 'Delhi', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            
            // Goa
            ['pincode' => '403001', 'city' => 'Panaji', 'state' => 'Goa', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Gujarat
            ['pincode' => '380001', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '395001', 'city' => 'Surat', 'state' => 'Gujarat', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '382001', 'city' => 'Vadodara', 'state' => 'Gujarat', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '360001', 'city' => 'Rajkot', 'state' => 'Gujarat', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            
            // Haryana
            ['pincode' => '122001', 'city' => 'Gurugram', 'state' => 'Haryana', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '134001', 'city' => 'Ambala', 'state' => 'Haryana', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            
            // Himachal Pradesh
            ['pincode' => '171001', 'city' => 'Shimla', 'state' => 'Himachal Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Jammu and Kashmir
            ['pincode' => '190001', 'city' => 'Srinagar', 'state' => 'Jammu and Kashmir', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            ['pincode' => '180001', 'city' => 'Jammu', 'state' => 'Jammu and Kashmir', 'country' => 'IN', 'estimated_days' => 4, 'cod_available' => 1, 'is_active' => 1],
            
            // Jharkhand
            ['pincode' => '834001', 'city' => 'Ranchi', 'state' => 'Jharkhand', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Karnataka
            ['pincode' => '560001', 'city' => 'Bangalore', 'state' => 'Karnataka', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '575001', 'city' => 'Mangalore', 'state' => 'Karnataka', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '576001', 'city' => 'Udupi', 'state' => 'Karnataka', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Kerala
            ['pincode' => '695001', 'city' => 'Thiruvananthapuram', 'state' => 'Kerala', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '682001', 'city' => 'Ernakulam', 'state' => 'Kerala', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '673001', 'city' => 'Kozhikode', 'state' => 'Kerala', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Ladakh
            ['pincode' => '194101', 'city' => 'Leh', 'state' => 'Ladakh', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            
            // Lakshadweep
            ['pincode' => '682555', 'city' => 'Kavaratti', 'state' => 'Lakshadweep', 'country' => 'IN', 'estimated_days' => 7, 'cod_available' => 0, 'is_active' => 1],
            
            // Madhya Pradesh
            ['pincode' => '452001', 'city' => 'Indore', 'state' => 'Madhya Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '462001', 'city' => 'Bhopal', 'state' => 'Madhya Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '482001', 'city' => 'Jabalpur', 'state' => 'Madhya Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Maharashtra
            ['pincode' => '400001', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '411001', 'city' => 'Pune', 'state' => 'Maharashtra', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '440001', 'city' => 'Nagpur', 'state' => 'Maharashtra', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '431001', 'city' => 'Aurangabad', 'state' => 'Maharashtra', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '422001', 'city' => 'Nashik', 'state' => 'Maharashtra', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            
            // Manipur
            ['pincode' => '795001', 'city' => 'Imphal', 'state' => 'Manipur', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            
            // Meghalaya
            ['pincode' => '793001', 'city' => 'Shillong', 'state' => 'Meghalaya', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 1, 'is_active' => 1],
            
            // Mizoram
            ['pincode' => '796001', 'city' => 'Aizawl', 'state' => 'Mizoram', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            
            // Nagaland
            ['pincode' => '797001', 'city' => 'Kohima', 'state' => 'Nagaland', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            
            // Odisha
            ['pincode' => '751001', 'city' => 'Bhubaneswar', 'state' => 'Odisha', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '752001', 'city' => 'Cuttack', 'state' => 'Odisha', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Puducherry
            ['pincode' => '605001', 'city' => 'Pondicherry', 'state' => 'Puducherry', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Punjab
            ['pincode' => '141001', 'city' => 'Ludhiana', 'state' => 'Punjab', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '144001', 'city' => 'Jalandhar', 'state' => 'Punjab', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '147001', 'city' => 'Patiala', 'state' => 'Punjab', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '160001', 'city' => 'Amritsar', 'state' => 'Punjab', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            
            // Rajasthan
            ['pincode' => '302001', 'city' => 'Jaipur', 'state' => 'Rajasthan', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '324001', 'city' => 'Kota', 'state' => 'Rajasthan', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '342001', 'city' => 'Jodhpur', 'state' => 'Rajasthan', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '334001', 'city' => 'Bikaner', 'state' => 'Rajasthan', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '311001', 'city' => 'Udaipur', 'state' => 'Rajasthan', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '340001', 'city' => 'Ajmer', 'state' => 'Rajasthan', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Sikkim
            ['pincode' => '737101', 'city' => 'Gangtok', 'state' => 'Sikkim', 'country' => 'IN', 'estimated_days' => 4, 'cod_available' => 0, 'is_active' => 1],
            
            // Tamil Nadu
            ['pincode' => '600001', 'city' => 'Chennai', 'state' => 'Tamil Nadu', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '641001', 'city' => 'Coimbatore', 'state' => 'Tamil Nadu', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '625001', 'city' => 'Madurai', 'state' => 'Tamil Nadu', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '636001', 'city' => 'Salem', 'state' => 'Tamil Nadu', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '642001', 'city' => 'Tirupur', 'state' => 'Tamil Nadu', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '603001', 'city' => 'Vellore', 'state' => 'Tamil Nadu', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Telangana
            ['pincode' => '500001', 'city' => 'Hyderabad', 'state' => 'Telangana', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '502001', 'city' => 'Warangal', 'state' => 'Telangana', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '509001', 'city' => 'Nizamabad', 'state' => 'Telangana', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Tripura
            ['pincode' => '799001', 'city' => 'Agartala', 'state' => 'Tripura', 'country' => 'IN', 'estimated_days' => 5, 'cod_available' => 0, 'is_active' => 1],
            
            // Uttar Pradesh
            ['pincode' => '226001', 'city' => 'Lucknow', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '201001', 'city' => 'Ghaziabad', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '201010', 'city' => 'Noida', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '211001', 'city' => 'Prayagraj', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '250001', 'city' => 'Agra', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '244001', 'city' => 'Moradabad', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '208001', 'city' => 'Kanpur', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '263001', 'city' => 'Haldwani', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '202002', 'city' => 'Aligarh', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '222001', 'city' => 'Jaunpur', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '247001', 'city' => 'Bijnor', 'state' => 'Uttar Pradesh', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // Uttarakhand
            ['pincode' => '248001', 'city' => 'Dehradun', 'state' => 'Uttarakhand', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '263001', 'city' => 'Haldwani', 'state' => 'Uttarakhand', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '244713', 'city' => 'Roorkee', 'state' => 'Uttarakhand', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            
            // West Bengal
            ['pincode' => '700001', 'city' => 'Kolkata', 'state' => 'West Bengal', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '700091', 'city' => 'Salt Lake', 'state' => 'West Bengal', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '713001', 'city' => 'Durgapur', 'state' => 'West Bengal', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '721301', 'city' => 'Siliguri', 'state' => 'West Bengal', 'country' => 'IN', 'estimated_days' => 3, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '711101', 'city' => 'Howrah', 'state' => 'West Bengal', 'country' => 'IN', 'estimated_days' => 2, 'cod_available' => 1, 'is_active' => 1],
            ['pincode' => '734001', 'city' => 'Darjeeling', 'state' => 'West Bengal', 'country' => 'IN', 'estimated_days' => 4, 'cod_available' => 1, 'is_active' => 1],
        ];
    }
}
