<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\ScopeIndentSniff;
use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer;

return static function (ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/common/array.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/common/control-structures.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/common/docblock.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/common/namespaces.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/common/strict.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/clean-code.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/psr12.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/php70.php');

    $containerConfigurator->import(__DIR__ . '/../../vendor/symplify/easy-coding-standard/config/set/php71.php');

    $parameters = $containerConfigurator->parameters();

    $parameters->set('indentation', 'spaces');

    $parameters->set('skip', [
        BlankLineAfterOpeningTagFixer::class => null,
        BracesFixer::class                   => null,
        BinaryOperatorSpacesFixer::class     => null,
        ScopeIndentSniff::class              => null,
    ]);

    $services = $containerConfigurator->services();

    $services->set(ReturnTypeDeclarationFixer::class)
        ->call('configure', [['space_before' => 'one']]);

    $services->set(ScopeIndentSniff::class)
        ->property('exact', false)
        ->property('indent', 2);

    $services->set(ParamReturnAndVarTagMalformsFixer::class);
};
