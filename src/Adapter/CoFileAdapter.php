<?php declare(strict_types=1);

namespace Swoft\Cache\Adapter;

use Swoft\Cache\Concern\CoFileSystemTrait;

/**
 * Class CoFileAdapter
 *
 * @since 2.0.8
 */
class CoFileAdapter extends FileAdapter
{
    use CoFileSystemTrait;
}
