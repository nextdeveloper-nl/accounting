<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\InvoiceItemsPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractInvoiceItemsPerspectiveTransformer;
use NextDeveloper\IAAS\Database\Models\IpAddresses;
use NextDeveloper\IAAS\Database\Models\VirtualDiskImages;
use NextDeveloper\IAAS\Database\Models\VirtualMachines;
use NextDeveloper\IAAS\Database\Models\VirtualNetworkCards;
use NextDeveloper\Marketplace\Database\Models\ProductCatalogs;
use NextDeveloper\Marketplace\Database\Models\Subscriptions;

/**
 * Class InvoiceItemsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class InvoiceItemsPerspectiveTransformer extends AbstractInvoiceItemsPerspectiveTransformer
{

    /**
     * @param InvoiceItemsPerspective $model
     *
     * @return array
     */
    public function transform(InvoiceItemsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('InvoiceItemsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            //return $transformed;
        }

        $transformed = parent::transform($model);
        $transformed['object_type'] = Str::replace('\Database\Models', '', $transformed['object_type']);

        switch ($transformed['object_type']) {
            case 'NextDeveloper\IAAS\VirtualMachines':
                $obj = VirtualMachines::withoutGlobalScopes()->where('id', $transformed['object_id'])->first();
                $transformed['object_name'] = $obj?->name;
                $transformed['object_id'] = $obj?->uuid;
                break;
            case 'NextDeveloper\IAAS\VirtualDiskImages':
                $obj = VirtualDiskImages::withoutGlobalScopes()->where('id', $transformed['object_id'])->first();
                $transformed['object_name'] = $obj?->name;
                $transformed['object_id'] = $obj?->uuid;
                break;
            case 'NextDeveloper\Marketplace\Subscriptions':
                $subObj = Subscriptions::withoutGlobalScopes()->where('id', $transformed['object_id'])->first();
                $obj = ProductCatalogs::withoutGlobalScopes()->where('id', $subObj->marketplace_product_catalog_id)->first();
                $transformed['object_name'] = $obj?->name;
                $transformed['object_id'] = $obj?->uuid;
                break;
            case 'NextDeveloper\IAAS\IpAddresses':
                $obj = IpAddresses::withoutGlobalScopes()->where('id', $transformed['object_id'])->first();
                $transformed['object_name'] = $obj?->ip_addr;
                $transformed['object_id'] = $obj?->uuid;
                break;
            case 'NextDeveloper\IAAS\VirtualNetworkCards':
                $subObj = VirtualNetworkCards::withoutGlobalScopes()->where('id', $transformed['object_id'])->first();
                $obj = VirtualMachines::withoutGlobalScopes()->where('id', $subObj->iaas_virtual_machine_id)->first();
                $transformed['object_name'] = 'Network card of: ' . $obj?->name;
                $transformed['object_id'] = $obj?->uuid;
                break;
            default:
                $transformed['object_id'] = null;
        }

        Cache::set(
            CacheHelper::getKey('InvoiceItemsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
