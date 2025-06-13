<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use App\Helpers\ObjectHelper;
use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\ContractItemsPerspective;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractContractItemsPerspectiveTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class ContractItemsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class ContractItemsPerspectiveTransformer extends AbstractContractItemsPerspectiveTransformer
{

    /**
     * @param ContractItemsPerspective $model
     *
     * @return array
     */
    public function transform(ContractItemsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('ContractItemsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        $obj = ObjectHelper::getObject($transformed['object_type'], $transformed['object_id']);

        switch ($transformed['object_type']) {
            case 'NextDeveloper\\IAAS\\Database\\Models\\VirtualMachines':
                $transformed['object_type'] = 'Server | ' . $obj->name;
                $transformed['object_id'] = $obj->uuid;
                break;
            case 'NextDeveloper\\IAAS\\Database\\Models\\VirtualDiskImages':
                $transformed['object_type'] = 'Disk | ' . $obj->name;
                $transformed['object_id'] = $obj->uuid;
                break;
            case 'NextDeveloper\\IAAS\\Database\\Models\\VirtualNetworkCards':
                $transformed['object_type'] = 'Network Card';
                $transformed['object_id'] = $obj->uuid;
                break;
            case 'NextDeveloper\\IAAS\\Database\\Models\\IpAddresses':
                $transformed['object_type'] = 'IP Address: ' . $obj->ip_addr;
                $transformed['object_id'] = $obj->uuid;
        }

        Cache::set(
            CacheHelper::getKey('ContractItemsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
