<?php
/**
 * This file is part of Phiremock.
 *
 * Phiremock is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Phiremock is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Phiremock.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Mcustiel\Phiremock\Common\Utils\V1;

use Mcustiel\Phiremock\Common\Utils\ArrayToResponseConverter as ArrayToResponseConverterInterface;
use Mcustiel\Phiremock\Domain\Options\Delay;
use Mcustiel\Phiremock\Domain\Options\ScenarioState;
use Mcustiel\Phiremock\Domain\Response;

abstract class ArrayToResponseConverter implements ArrayToResponseConverterInterface
{
    const ALLOWED_OPTIONS = ['delayMillis'=> null];
    const NO_DELAY = 0;

    public function convert(array $responseArray): Response
    {
        return $this->convertResponse(
            $responseArray,
            $this->getDelay($responseArray),
            $this->getNewScenarioState($responseArray)
        );
    }

    protected function ensureNotInvalidOptionsAreProvided(array $responseArray): void
    {
        $invalidOptions = array_diff_key($responseArray, static::ALLOWED_OPTIONS);
        if (!empty($invalidOptions)) {
            throw new \Exception('Unknown expectation options: ' . var_export($invalidOptions, true));
        }
    }

    abstract protected function convertResponse(
        array $response,
        ?Delay $delay,
        ?ScenarioState $newScenarioState
    ): Response;

    private function getDelay(array $responseArray): ?Delay
    {
        if (empty($responseArray['response'])) {
            return null;
        }

        $responseArray = $responseArray['response'];
        if (!empty($responseArray['delayMillis'])) {
            return new Delay($responseArray['delayMillis']);
        }

        return null;
    }

    private function getNewScenarioState(array $expectationArray): ?ScenarioState
    {
        $newScenarioState = null;
        if (!empty($expectationArray['newScenarioState'])) {
            $newScenarioState = new ScenarioState($expectationArray['newScenarioState']);
        }

        return $newScenarioState;
    }
}
