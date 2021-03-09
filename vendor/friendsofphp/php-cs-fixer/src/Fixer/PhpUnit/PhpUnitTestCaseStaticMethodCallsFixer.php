<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace MolliePrefix\PhpCsFixer\Fixer\PhpUnit;

use MolliePrefix\PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use MolliePrefix\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface;
use MolliePrefix\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample;
use MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition;
use MolliePrefix\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use MolliePrefix\PhpCsFixer\Tokenizer\Token;
use MolliePrefix\PhpCsFixer\Tokenizer\Tokens;
use MolliePrefix\PhpCsFixer\Tokenizer\TokensAnalyzer;
use MolliePrefix\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpUnitTestCaseStaticMethodCallsFixer extends \MolliePrefix\PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \MolliePrefix\PhpCsFixer\Fixer\ConfigurationDefinitionFixerInterface
{
    /**
     * @internal
     */
    const CALL_TYPE_THIS = 'this';
    /**
     * @internal
     */
    const CALL_TYPE_SELF = 'self';
    /**
     * @internal
     */
    const CALL_TYPE_STATIC = 'static';
    private $allowedValues = [self::CALL_TYPE_THIS => \true, self::CALL_TYPE_SELF => \true, self::CALL_TYPE_STATIC => \true];
    private $staticMethods = [
        // Assert methods
        'anything' => \true,
        'arrayHasKey' => \true,
        'assertArrayHasKey' => \true,
        'assertArrayNotHasKey' => \true,
        'assertArraySubset' => \true,
        'assertAttributeContains' => \true,
        'assertAttributeContainsOnly' => \true,
        'assertAttributeCount' => \true,
        'assertAttributeEmpty' => \true,
        'assertAttributeEquals' => \true,
        'assertAttributeGreaterThan' => \true,
        'assertAttributeGreaterThanOrEqual' => \true,
        'assertAttributeInstanceOf' => \true,
        'assertAttributeInternalType' => \true,
        'assertAttributeLessThan' => \true,
        'assertAttributeLessThanOrEqual' => \true,
        'assertAttributeNotContains' => \true,
        'assertAttributeNotContainsOnly' => \true,
        'assertAttributeNotCount' => \true,
        'assertAttributeNotEmpty' => \true,
        'assertAttributeNotEquals' => \true,
        'assertAttributeNotInstanceOf' => \true,
        'assertAttributeNotInternalType' => \true,
        'assertAttributeNotSame' => \true,
        'assertAttributeSame' => \true,
        'assertClassHasAttribute' => \true,
        'assertClassHasStaticAttribute' => \true,
        'assertClassNotHasAttribute' => \true,
        'assertClassNotHasStaticAttribute' => \true,
        'assertContains' => \true,
        'assertContainsEquals' => \true,
        'assertContainsOnly' => \true,
        'assertContainsOnlyInstancesOf' => \true,
        'assertCount' => \true,
        'assertDirectoryDoesNotExist' => \true,
        'assertDirectoryExists' => \true,
        'assertDirectoryIsNotReadable' => \true,
        'assertDirectoryIsNotWritable' => \true,
        'assertDirectoryIsReadable' => \true,
        'assertDirectoryIsWritable' => \true,
        'assertDirectoryNotExists' => \true,
        'assertDirectoryNotIsReadable' => \true,
        'assertDirectoryNotIsWritable' => \true,
        'assertDoesNotMatchRegularExpression' => \true,
        'assertEmpty' => \true,
        'assertEqualXMLStructure' => \true,
        'assertEquals' => \true,
        'assertEqualsCanonicalizing' => \true,
        'assertEqualsIgnoringCase' => \true,
        'assertEqualsWithDelta' => \true,
        'assertFalse' => \true,
        'assertFileDoesNotExist' => \true,
        'assertFileEquals' => \true,
        'assertFileEqualsCanonicalizing' => \true,
        'assertFileEqualsIgnoringCase' => \true,
        'assertFileExists' => \true,
        'assertFileIsNotReadable' => \true,
        'assertFileIsNotWritable' => \true,
        'assertFileIsReadable' => \true,
        'assertFileIsWritable' => \true,
        'assertFileNotEquals' => \true,
        'assertFileNotEqualsCanonicalizing' => \true,
        'assertFileNotEqualsIgnoringCase' => \true,
        'assertFileNotExists' => \true,
        'assertFileNotIsReadable' => \true,
        'assertFileNotIsWritable' => \true,
        'assertFinite' => \true,
        'assertGreaterThan' => \true,
        'assertGreaterThanOrEqual' => \true,
        'assertInfinite' => \true,
        'assertInstanceOf' => \true,
        'assertInternalType' => \true,
        'assertIsArray' => \true,
        'assertIsBool' => \true,
        'assertIsCallable' => \true,
        'assertIsClosedResource' => \true,
        'assertIsFloat' => \true,
        'assertIsInt' => \true,
        'assertIsIterable' => \true,
        'assertIsNotArray' => \true,
        'assertIsNotBool' => \true,
        'assertIsNotCallable' => \true,
        'assertIsNotClosedResource' => \true,
        'assertIsNotFloat' => \true,
        'assertIsNotInt' => \true,
        'assertIsNotIterable' => \true,
        'assertIsNotNumeric' => \true,
        'assertIsNotObject' => \true,
        'assertIsNotReadable' => \true,
        'assertIsNotResource' => \true,
        'assertIsNotScalar' => \true,
        'assertIsNotString' => \true,
        'assertIsNotWritable' => \true,
        'assertIsNumeric' => \true,
        'assertIsObject' => \true,
        'assertIsReadable' => \true,
        'assertIsResource' => \true,
        'assertIsScalar' => \true,
        'assertIsString' => \true,
        'assertIsWritable' => \true,
        'assertJson' => \true,
        'assertJsonFileEqualsJsonFile' => \true,
        'assertJsonFileNotEqualsJsonFile' => \true,
        'assertJsonStringEqualsJsonFile' => \true,
        'assertJsonStringEqualsJsonString' => \true,
        'assertJsonStringNotEqualsJsonFile' => \true,
        'assertJsonStringNotEqualsJsonString' => \true,
        'assertLessThan' => \true,
        'assertLessThanOrEqual' => \true,
        'assertMatchesRegularExpression' => \true,
        'assertNan' => \true,
        'assertNotContains' => \true,
        'assertNotContainsEquals' => \true,
        'assertNotContainsOnly' => \true,
        'assertNotCount' => \true,
        'assertNotEmpty' => \true,
        'assertNotEquals' => \true,
        'assertNotEqualsCanonicalizing' => \true,
        'assertNotEqualsIgnoringCase' => \true,
        'assertNotEqualsWithDelta' => \true,
        'assertNotFalse' => \true,
        'assertNotInstanceOf' => \true,
        'assertNotInternalType' => \true,
        'assertNotIsReadable' => \true,
        'assertNotIsWritable' => \true,
        'assertNotNull' => \true,
        'assertNotRegExp' => \true,
        'assertNotSame' => \true,
        'assertNotSameSize' => \true,
        'assertNotTrue' => \true,
        'assertNull' => \true,
        'assertObjectEquals' => \true,
        'assertObjectHasAttribute' => \true,
        'assertObjectNotHasAttribute' => \true,
        'assertRegExp' => \true,
        'assertSame' => \true,
        'assertSameSize' => \true,
        'assertStringContainsString' => \true,
        'assertStringContainsStringIgnoringCase' => \true,
        'assertStringEndsNotWith' => \true,
        'assertStringEndsWith' => \true,
        'assertStringEqualsFile' => \true,
        'assertStringEqualsFileCanonicalizing' => \true,
        'assertStringEqualsFileIgnoringCase' => \true,
        'assertStringMatchesFormat' => \true,
        'assertStringMatchesFormatFile' => \true,
        'assertStringNotContainsString' => \true,
        'assertStringNotContainsStringIgnoringCase' => \true,
        'assertStringNotEqualsFile' => \true,
        'assertStringNotEqualsFileCanonicalizing' => \true,
        'assertStringNotEqualsFileIgnoringCase' => \true,
        'assertStringNotMatchesFormat' => \true,
        'assertStringNotMatchesFormatFile' => \true,
        'assertStringStartsNotWith' => \true,
        'assertStringStartsWith' => \true,
        'assertThat' => \true,
        'assertTrue' => \true,
        'assertXmlFileEqualsXmlFile' => \true,
        'assertXmlFileNotEqualsXmlFile' => \true,
        'assertXmlStringEqualsXmlFile' => \true,
        'assertXmlStringEqualsXmlString' => \true,
        'assertXmlStringNotEqualsXmlFile' => \true,
        'assertXmlStringNotEqualsXmlString' => \true,
        'attribute' => \true,
        'attributeEqualTo' => \true,
        'callback' => \true,
        'classHasAttribute' => \true,
        'classHasStaticAttribute' => \true,
        'contains' => \true,
        'containsEqual' => \true,
        'containsIdentical' => \true,
        'containsOnly' => \true,
        'containsOnlyInstancesOf' => \true,
        'countOf' => \true,
        'directoryExists' => \true,
        'equalTo' => \true,
        'equalToCanonicalizing' => \true,
        'equalToIgnoringCase' => \true,
        'equalToWithDelta' => \true,
        'fail' => \true,
        'fileExists' => \true,
        'getCount' => \true,
        'getObjectAttribute' => \true,
        'getStaticAttribute' => \true,
        'greaterThan' => \true,
        'greaterThanOrEqual' => \true,
        'identicalTo' => \true,
        'isEmpty' => \true,
        'isFalse' => \true,
        'isFinite' => \true,
        'isInfinite' => \true,
        'isInstanceOf' => \true,
        'isJson' => \true,
        'isNan' => \true,
        'isNull' => \true,
        'isReadable' => \true,
        'isTrue' => \true,
        'isType' => \true,
        'isWritable' => \true,
        'lessThan' => \true,
        'lessThanOrEqual' => \true,
        'logicalAnd' => \true,
        'logicalNot' => \true,
        'logicalOr' => \true,
        'logicalXor' => \true,
        'markTestIncomplete' => \true,
        'markTestSkipped' => \true,
        'matches' => \true,
        'matchesRegularExpression' => \true,
        'objectEquals' => \true,
        'objectHasAttribute' => \true,
        'readAttribute' => \true,
        'resetCount' => \true,
        'stringContains' => \true,
        'stringEndsWith' => \true,
        'stringStartsWith' => \true,
        // TestCase methods
        'any' => \true,
        'at' => \true,
        'atLeast' => \true,
        'atLeastOnce' => \true,
        'atMost' => \true,
        'exactly' => \true,
        'never' => \true,
        'once' => \true,
        'onConsecutiveCalls' => \true,
        'returnArgument' => \true,
        'returnCallback' => \true,
        'returnSelf' => \true,
        'returnValue' => \true,
        'returnValueMap' => \true,
        'setUpBeforeClass' => \true,
        'tearDownAfterClass' => \true,
        'throwException' => \true,
    ];
    private $conversionMap = [self::CALL_TYPE_THIS => [[\T_OBJECT_OPERATOR, '->'], [\T_VARIABLE, '$this']], self::CALL_TYPE_SELF => [[\T_DOUBLE_COLON, '::'], [\T_STRING, 'self']], self::CALL_TYPE_STATIC => [[\T_DOUBLE_COLON, '::'], [\T_STATIC, 'static']]];
    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        $codeSample = '<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testMe()
    {
        $this->assertSame(1, 2);
        self::assertSame(1, 2);
        static::assertSame(1, 2);
    }
}
';
        return new \MolliePrefix\PhpCsFixer\FixerDefinition\FixerDefinition('Calls to `PHPUnit\\Framework\\TestCase` static methods must all be of the same type, either `$this->`, `self::` or `static::`.', [new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample($codeSample), new \MolliePrefix\PhpCsFixer\FixerDefinition\CodeSample($codeSample, ['call_type' => self::CALL_TYPE_THIS])], null, 'Risky when PHPUnit methods are overridden or not accessible, or when project has PHPUnit incompatibilities.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before FinalStaticAccessFixer, SelfStaticAccessorFixer.
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition()
    {
        $thisFixer = $this;
        return new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('call_type', 'The call type to use for referring to PHPUnit methods.'))->setAllowedTypes(['string'])->setAllowedValues(\array_keys($this->allowedValues))->setDefault('static')->getOption(), (new \MolliePrefix\PhpCsFixer\FixerConfiguration\FixerOptionBuilder('methods', 'Dictionary of `method` => `call_type` values that differ from the default strategy.'))->setAllowedTypes(['array'])->setAllowedValues([static function ($option) use($thisFixer) {
            foreach ($option as $method => $value) {
                if (!isset($thisFixer->staticMethods[$method])) {
                    throw new \MolliePrefix\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Unexpected "methods" key, expected any of "%s", got "%s".', \implode('", "', \array_keys($thisFixer->staticMethods)), \is_object($method) ? \get_class($method) : \gettype($method) . '#' . $method));
                }
                if (!isset($thisFixer->allowedValues[$value])) {
                    throw new \MolliePrefix\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Unexpected value for method "%s", expected any of "%s", got "%s".', $method, \implode('", "', \array_keys($thisFixer->allowedValues)), \is_object($value) ? \get_class($value) : (null === $value ? 'null' : \gettype($value) . '#' . $value)));
                }
            }
            return \true;
        }])->setDefault([])->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $analyzer = new \MolliePrefix\PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($index = $startIndex; $index < $endIndex; ++$index) {
            // skip anonymous classes
            if ($tokens[$index]->isGivenKind(\T_CLASS)) {
                $index = $this->findEndOfNextBlock($tokens, $index);
                continue;
            }
            $callType = $this->configuration['call_type'];
            if ($tokens[$index]->isGivenKind(\T_FUNCTION)) {
                // skip lambda
                if ($analyzer->isLambda($index)) {
                    $index = $this->findEndOfNextBlock($tokens, $index);
                    continue;
                }
                // do not change `self` to `this` in static methods
                if ('this' === $callType) {
                    $attributes = $analyzer->getMethodAttributes($index);
                    if (\false !== $attributes['static']) {
                        $index = $this->findEndOfNextBlock($tokens, $index);
                        continue;
                    }
                }
            }
            if (!$tokens[$index]->isGivenKind(\T_STRING) || !isset($this->staticMethods[$tokens[$index]->getContent()])) {
                continue;
            }
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$nextIndex]->equals('(')) {
                $index = $nextIndex;
                continue;
            }
            $methodName = $tokens[$index]->getContent();
            if (isset($this->configuration['methods'][$methodName])) {
                $callType = $this->configuration['methods'][$methodName];
            }
            $operatorIndex = $tokens->getPrevMeaningfulToken($index);
            $referenceIndex = $tokens->getPrevMeaningfulToken($operatorIndex);
            if (!$this->needsConversion($tokens, $index, $referenceIndex, $callType)) {
                continue;
            }
            $tokens[$operatorIndex] = new \MolliePrefix\PhpCsFixer\Tokenizer\Token($this->conversionMap[$callType][0]);
            $tokens[$referenceIndex] = new \MolliePrefix\PhpCsFixer\Tokenizer\Token($this->conversionMap[$callType][1]);
        }
    }
    /**
     * @param int    $index
     * @param int    $referenceIndex
     * @param string $callType
     *
     * @return bool
     */
    private function needsConversion(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $referenceIndex, $callType)
    {
        $functionsAnalyzer = new \MolliePrefix\PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        return $functionsAnalyzer->isTheSameClassCall($tokens, $index) && !$tokens[$referenceIndex]->equals($this->conversionMap[$callType][1], \false);
    }
    /**
     * @param int $index
     *
     * @return int
     */
    private function findEndOfNextBlock(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = $tokens->getNextTokenOfKind($index, ['{']);
        return $tokens->findBlockEnd(\MolliePrefix\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
    }
}