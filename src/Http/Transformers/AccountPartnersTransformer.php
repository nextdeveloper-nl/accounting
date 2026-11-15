<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\AccountPartners;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractAccountPartnersTransformer;

/**
 * Class AccountPartnersTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AccountPartnersTransformer extends AbstractAccountPartnersTransformer
{

    /**
     * @param AccountPartners $model
     *
     * @return array
     */
    public function transform(AccountPartners $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('AccountPartners', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('AccountPartners', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
