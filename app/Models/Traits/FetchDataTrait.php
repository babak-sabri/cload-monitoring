<?php
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Helpers\Arr;
use App\Helpers\PaginateHelper;

trait FetchDataTrait
{
	/**
	 * paginate data using params
	 *
	 * @param Builder $query
	 * @param array $params
	 * @return Builder
	 */
	public function scopeFetchData(Builder $query, array $params=[])
	{
		foreach ($params as $key=>$value) {
			switch (Arr::get($this->searchale, "{$key}.type", null)) {
				case PaginateHelper::SCALAR_TYPE:
					if(Arr::isArray($value)) {
						$query->whereIn($key, $value);
					} else {
						$query->where($key, $value);
					}
					break;
				case PaginateHelper::LIKE_STRING_TYPE:
					$query->where($key, 'like', "%{$value}%");
					break;
				case PaginateHelper::BETWEEN_TYPE:
					$query->whereBetween($key, $value);
					break;
				case PaginateHelper::CLOSURE_TYPE:
					$query->$key($value);
					break;
			}

			switch ($key) {
				case PaginateHelper::SELECT_FIELDS:
					$query->select($value);
					break;
				case PaginateHelper::GROUP_BY:
					$query->groupBy($value);
					break;
				case PaginateHelper::SORT:
					foreach($value as $sortRow) {
						$query->orderBy($sortRow['field'], Arr::get($sortRow, 'type', self::ASC_SORT));
					}
					break;
			}
		}
		return $query;
	}
}