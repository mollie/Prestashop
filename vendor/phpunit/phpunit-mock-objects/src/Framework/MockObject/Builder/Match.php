<?php

namespace MolliePrefix;

/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Builder interface for invocation order matches.
 *
 * @since Interface available since Release 1.0.0
 */
interface PHPUnit_Framework_MockObject_Builder_Match extends \MolliePrefix\PHPUnit_Framework_MockObject_Builder_Stub
{
    /**
     * Defines the expectation which must occur before the current is valid.
     *
     * @param string $id The identification of the expectation that should
     *                   occur before this one.
     *
     * @return PHPUnit_Framework_MockObject_Builder_Stub
     */
    public function after($id);
}
/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Builder interface for invocation order matches.
 *
 * @since Interface available since Release 1.0.0
 */
\class_alias('MolliePrefix\\PHPUnit_Framework_MockObject_Builder_Match', 'MolliePrefix\\PHPUnit_Framework_MockObject_Builder_Match', \false);
