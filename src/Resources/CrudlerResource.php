<?php

namespace Crudler\Resources;

use Crudler\Resources\DTO\CrudlerResourceGeneratorDTO;
use Crudler\Resources\Core\CoreCrudlerResource;

class CrudlerResource {
    public CrudlerResourceGeneratorDTO $dto;

    public function __construct(CrudlerResourceGeneratorDTO $dto) {
        $this->dto = $dto;
    }

    /**
     * Summary of resource
     *
     * @param mixed $resource
     * @param array<string> $additionalFields
     *
     * @return CoreCrudlerResource
     */
    public function resource($resource, array $additionalFields = []): CoreCrudlerResource {
        $selectedRaw = $this->getSelectedRaw($additionalFields);

        $newDto = $this->dto->setAdditionalData($selectedRaw);

        return new CoreCrudlerResource(
            $resource,
            $newDto
        );
    }

    /**
     * Summary of collection
     *
     * @param mixed $resource
     * @param array<string> $additionalFields

     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function collection($resource, array $additionalFields = [])
    {
        // Обработка пагинатора: извлекаем только items (коллекцию моделей)
        if ($resource instanceof \Illuminate\Pagination\AbstractPaginator) {
            $resource = $resource->getCollection();
        }

        $selectedRaw = $this->getSelectedRaw($additionalFields);

        $newDto = $this->dto->setAdditionalData($selectedRaw);

        $items = collect($resource)->map(
            fn($item) => new CoreCrudlerResource($item, $newDto)
        );

        return new \Illuminate\Http\Resources\Json\ResourceCollection($items);
    }

    private function getSelectedRaw(array $additionalFields = [])
    {
        // Получаем raw additionalData из текущего DTO (closures и т.д.)
        $allAdditionalRaw = [];
        foreach ($this->dto->additionalData as $key => $dtoItem) {
            $allAdditionalRaw[$key] = $dtoItem->value;
        }

        // Если $additionalFields не пуст — фильтруем по ключам
        $selectedRaw = [];
        if (!empty($additionalFields)) {
            foreach ($additionalFields as $field) {
                if (array_key_exists($field, $allAdditionalRaw)) {
                    $selectedRaw[$field] = $allAdditionalRaw[$field];
                }
            }
        } else {
            // Если пусто — берём все
            $selectedRaw = $allAdditionalRaw;
        }

        return $selectedRaw;
    }
}
