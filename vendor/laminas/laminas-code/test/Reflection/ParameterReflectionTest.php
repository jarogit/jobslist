<?php

namespace LaminasTest\Code\Reflection;

use Closure;
use Laminas\Code\Reflection;
use Laminas\Code\Reflection\ClassReflection;
use LaminasTest\Code\Reflection\TestAsset\ClassWithPromotedParameter;
use LaminasTest\Code\TestAsset\ClassTypeHintedClass;
use LaminasTest\Code\TestAsset\DocBlockOnlyHintsClass;
use LaminasTest\Code\TestAsset\InternalHintsClass;
use PHPUnit\Framework\TestCase;
use ReflectionType;

/**
 * @group Laminas_Reflection
 * @group Laminas_Reflection_Parameter
 */
class ParameterReflectionTest extends TestCase
{
    public function testDeclaringClassReturn()
    {
        $parameter = new Reflection\ParameterReflection(
            [TestAsset\TestSampleClass2::class, 'getProp2'],
            0
        );
        self::assertInstanceOf(ClassReflection::class, $parameter->getDeclaringClass());
    }

    public function testClassReturnNoClassGivenReturnsNull()
    {
        $parameter = new Reflection\ParameterReflection(
            [TestAsset\TestSampleClass2::class, 'getProp2'],
            'param1'
        );
        self::assertNull($parameter->getClass());
    }

    public function testClassReturn()
    {
        $parameter = new Reflection\ParameterReflection(
            [TestAsset\TestSampleClass2::class, 'getProp2'],
            'param2'
        );
        self::assertInstanceOf(ClassReflection::class, $parameter->getClass());
    }

    /**
     * @dataProvider paramType
     * @param string $param
     * @param string $type
     */
    public function testTypeReturn($param, $type)
    {
        $parameter = new Reflection\ParameterReflection(
            [TestAsset\TestSampleClass5::class, 'doSomething'],
            $param
        );
        self::assertEquals($type, $parameter->detectType());
    }

    /**
     * This test covers type detection when not all params declared in phpDoc block
     *
     * @dataProvider paramTypeWithNotAllParamsDeclared
     */
    public function testTypeReturnWithNotAllParamsDeclared(string $param, string $type): void
    {
        $parameter = new Reflection\ParameterReflection(
            [TestAsset\TestSampleClass5::class, 'methodWithNotAllParamsDeclared'],
            $param
        );
        self::assertEquals($type, $parameter->detectType());
    }

    public function testCallableTypeHint()
    {
        $parameter = new Reflection\ParameterReflection(
            [TestAsset\CallableTypeHintClass::class, 'foo'],
            'bar'
        );
        self::assertEquals('callable', $parameter->detectType());
    }

    /**
     * @return string[][]
     * @psalm-return non-empty-list<array{non-empty-string, non-empty-string}>
     */
    public function paramType(): array
    {
        return [
            ['one', 'int'],
            ['two', 'int'],
            ['three', 'string'],
            ['array', 'array'],
            ['class', TestAsset\TestSampleClass::class],
        ];
    }

    /**
     * @return string[][]
     * @psalm-return non-empty-list<array{non-empty-string, non-empty-string}>
     */
    public function paramTypeWithNotAllParamsDeclared(): array
    {
        return [
            ['one', 'string'],
            ['two', 'string'],
            ['three', 'int'],
            ['four', 'string'],
            ['five', 'string'],
        ];
    }

    /**
     * @group zendframework/zend-code#29
     * @dataProvider reflectionHints
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @param string $expectedType
     */
    public function testGetType($className, $methodName, $parameterName, $expectedType)
    {
        $reflection = new Reflection\ParameterReflection(
            [$className, $methodName],
            $parameterName
        );

        $type = $reflection->getType();

        self::assertInstanceOf(ReflectionType::class, $type);
        self::assertSame($expectedType, $type->getName());
    }

    /**
     * @group zendframework/zend-code#29
     * @dataProvider reflectionHints
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @param string $expectedType
     */
    public function testDetectType($className, $methodName, $parameterName, $expectedType)
    {
        $reflection = new Reflection\ParameterReflection(
            [$className, $methodName],
            $parameterName
        );

        // following is just due to an incompatibility between this test method and `testGetType`
        if ('self' === $expectedType) {
            $expectedType = $className;
        }

        self::assertSame($expectedType, $reflection->detectType());
    }

    /**
     * @return string[][]
     */
    public function reflectionHints()
    {
        return [
            [InternalHintsClass::class, 'arrayParameter', 'foo', 'array'],
            [InternalHintsClass::class, 'callableParameter', 'foo', 'callable'],
            [InternalHintsClass::class, 'intParameter', 'foo', 'int'],
            [InternalHintsClass::class, 'floatParameter', 'foo', 'float'],
            [InternalHintsClass::class, 'stringParameter', 'foo', 'string'],
            [InternalHintsClass::class, 'boolParameter', 'foo', 'bool'],
            [ClassTypeHintedClass::class, 'selfParameter', 'foo', 'self'],
            [ClassTypeHintedClass::class, 'classParameter', 'foo', ClassTypeHintedClass::class],
            [ClassTypeHintedClass::class, 'otherClassParameter', 'foo', InternalHintsClass::class],
            [ClassTypeHintedClass::class, 'closureParameter', 'foo', Closure::class],
            [ClassTypeHintedClass::class, 'importedClosureParameter', 'foo', Closure::class],
        ];
    }

    /**
     * @group zendframework/zend-code#29
     * @dataProvider docBlockHints
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     */
    public function testGetTypeWithDocBlockOnlyTypes($className, $methodName, $parameterName)
    {
        $reflection = new Reflection\ParameterReflection(
            [$className, $methodName],
            $parameterName
        );

        self::assertNull($reflection->getType());
    }

    /**
     * @group zendframework/zend-code#29
     * @dataProvider docBlockHints
     * @param string $className
     * @param string $methodName
     * @param string $parameterName
     * @param string $expectedType
     */
    public function testDetectTypeWithDocBlockOnlyTypes($className, $methodName, $parameterName, $expectedType)
    {
        $reflection = new Reflection\ParameterReflection(
            [$className, $methodName],
            $parameterName
        );

        self::assertSame($expectedType, $reflection->detectType());
    }

    /**
     * @return string[][]
     */
    public function docBlockHints()
    {
        return [
            [DocBlockOnlyHintsClass::class, 'arrayParameter', 'foo', 'array'],
            [DocBlockOnlyHintsClass::class, 'callableParameter', 'foo', 'callable'],
            [DocBlockOnlyHintsClass::class, 'intParameter', 'foo', 'int'],
            [DocBlockOnlyHintsClass::class, 'floatParameter', 'foo', 'float'],
            [DocBlockOnlyHintsClass::class, 'stringParameter', 'foo', 'string'],
            [DocBlockOnlyHintsClass::class, 'boolParameter', 'foo', 'bool'],
            [DocBlockOnlyHintsClass::class, 'selfParameter', 'foo', 'self'],
            [DocBlockOnlyHintsClass::class, 'classParameter', 'foo', 'DocBlockOnlyHintsClass'],
            [DocBlockOnlyHintsClass::class, 'otherClassParameter', 'foo', 'InternalHintsClass'],
        ];
    }

    /** @requires PHP >= 8.0 */
    public function testPromotedParameter(): void
    {
        $reflection = new Reflection\ParameterReflection(
            [ClassWithPromotedParameter::class, '__construct'],
            'promotedParameter'
        );

        self::assertTrue($reflection->isPromoted());
        self::assertTrue($reflection->isPrivatePromoted());
        self::assertFalse($reflection->isProtectedPromoted());
        self::assertFalse($reflection->isPublicPromoted());
    }
}
