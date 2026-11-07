# 3 Rules Overview

<br>

## Categories

- [CategoryA](#categorya) (1)

- [CategoryB](#categoryb) (2)

<br>

## CategoryA

### SecondRector

Use `$new` instead of `$old`

- class: [`Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\DirectoryToMarkdownPrinter\Source\Rules\CategoryA\General\SecondRector`](Source/Rules/CategoryA/General/SecondRector.php)

```diff
-$old = 1;
+$new = 1;
 $same = 2;
```

<br>

## CategoryB

### ConfigurableRector

Rename the configured class

:wrench: **configure it!**

- class: [`Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\DirectoryToMarkdownPrinter\Source\Rules\CategoryB\v0\ConfigurableRector`](Source/Rules/CategoryB/v0/ConfigurableRector.php)

```diff
-new OldClass();
+new NewClass();
```

<br>

### FirstRector

Replace `someMethod()` by `otherMethod()`

- class: [`Ssch\TYPO3Rector\RuleDocGenerator\Tests\Printer\DirectoryToMarkdownPrinter\Source\Rules\CategoryB\v0\FirstRector`](Source/Rules/CategoryB/v0/FirstRector.php)

```diff
-$old = 1;
+$new = 1;
 $same = 2;
```

<br>
