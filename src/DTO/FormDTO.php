<?php

namespace Crudler\DTO;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Converter\DTO\ConverterDTO;

abstract class FormDTO {
    public function __construct(
        public readonly Authenticatable $user,
        public int $organizationId,
        public bool $userIsBase,
        public ?int $id = null,
    ) {}

    /**
     * processBaseData
     *
     * @param Request $request
     * @param ?int $entityId
     * @param array $customFields = []
     * @param bool $determineOrganizationId = true
     *
     * @return array
     */
    protected static function processBaseData(
        Request $request,
        ?int $entityId,
        array $customFields = [],
        bool $determineOrganizationId = true,
    ): array {
        $user = Auth::user();
        $requestFields = array_merge(
            static::getCommonRequestFields(),
            $customFields
        );
        $convertedData = (new ConverterDTO())->getRequestData(
            $request->only($requestFields)
        );

        return [
            'user' => $user,
            'organization_id' => $determineOrganizationId
                ? static::determineOrganizationId($user, $convertedData)
                : null,
            'user_is_base' => $user->role->is_base,
            'id' => $entityId,
            'converted_data' => $convertedData,
        ];
    }

    /**
     * getCommonRequestFields
     *
     * @return array
     */
    protected static function getCommonRequestFields(): array
    {
        return ['organization_id'];
    }

    /**
     * determineOrganizationId
     *
     * @param Authenticatable $user
     * @param array $convertedData
     *
     * @return int
     */
    protected static function determineOrganizationId(
        Authenticatable $user,
        array $convertedData
    ): int {
        $authRole = $user->role;

        return ($authRole->is_base && !empty($convertedData['organization_id']))
            ? $convertedData['organization_id']
            : $authRole->organization_id;
    }
}