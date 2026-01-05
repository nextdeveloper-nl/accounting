<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\AccountPartnerLogs;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractAccountPartnerLogsTransformer;

/**
 * Class AccountPartnerLogsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AccountPartnerLogsTransformer extends AbstractAccountPartnerLogsTransformer
{

    /**
     * @param AccountPartnerLogs $model
     *
     * @return array
     */
    public function transform(AccountPartnerLogs $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('AccountPartnerLogs', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('AccountPartnerLogs', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}
