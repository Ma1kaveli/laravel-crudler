<?php

namespace Crudler\Actions\Constants;

enum CrudlerPlaceUniqueEnum
{
    case default;

    case before_validation;

    case after_validation;

    case before_with_validation;

    case after_with_validation;

    case before_custom_validation;

    case after_custom_validation;

    case none;
}
