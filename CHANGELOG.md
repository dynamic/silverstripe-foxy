# Changelog

## [1.3.2](https://github.com/dynamic/silverstripe-foxy/tree/1.3.2) (2020-12-17)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.3.1...1.3.2)

**Merged pull requests:**

- ENHANCEMENT ProductExpirationHelper [\#121](https://github.com/dynamic/silverstripe-foxy/pull/121) ([muskie9](https://github.com/muskie9))
- CI Workflow - phpcs - standard [\#120](https://github.com/dynamic/silverstripe-foxy/pull/120) ([jsirish](https://github.com/jsirish))
- CI remove Jenkins [\#119](https://github.com/dynamic/silverstripe-foxy/pull/119) ([jsirish](https://github.com/jsirish))

## [1.3.1](https://github.com/dynamic/silverstripe-foxy/tree/1.3.1) (2020-08-07)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.3.0...1.3.1)

**Merged pull requests:**

- BUGFIX Purchasable isAvailable - Variations count [\#108](https://github.com/dynamic/silverstripe-foxy/pull/108) ([jsirish](https://github.com/jsirish))

## [1.3.0](https://github.com/dynamic/silverstripe-foxy/tree/1.3.0) (2020-07-24)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.2.5...1.3.0)

**Merged pull requests:**

- REFACTOR remove product options [\#104](https://github.com/dynamic/silverstripe-foxy/pull/104) ([jsirish](https://github.com/jsirish))

## [1.2.5](https://github.com/dynamic/silverstripe-foxy/tree/1.2.5) (2020-07-24)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.2.4...1.2.5)

**Merged pull requests:**

- BUGFIX update composer to remove vcs [\#107](https://github.com/dynamic/silverstripe-foxy/pull/107) ([jsirish](https://github.com/jsirish))

## [1.2.4](https://github.com/dynamic/silverstripe-foxy/tree/1.2.4) (2020-07-24)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.2.3...1.2.4)

**Merged pull requests:**

- REFACTOR remove VCS for Groupable Gridfield [\#105](https://github.com/dynamic/silverstripe-foxy/pull/105) ([jsirish](https://github.com/jsirish))

## [1.2.3](https://github.com/dynamic/silverstripe-foxy/tree/1.2.3) (2020-07-15)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.2.2...1.2.3)

**Merged pull requests:**

- BUGFIX replace refs of Options with Variations [\#103](https://github.com/dynamic/silverstripe-foxy/pull/103) ([jsirish](https://github.com/jsirish))

## [1.2.2](https://github.com/dynamic/silverstripe-foxy/tree/1.2.2) (2020-07-14)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.2.1...1.2.2)

**Fixed bugs:**

- BUG - values of 0 fail to hash properly due to being considered 'open' fields [\#87](https://github.com/dynamic/silverstripe-foxy/issues/87)

**Merged pull requests:**

- BUGFIX Update tests after Variations implementaiton [\#102](https://github.com/dynamic/silverstripe-foxy/pull/102) ([jsirish](https://github.com/jsirish))
- Fixed 0 values defaulting to being user editable [\#88](https://github.com/dynamic/silverstripe-foxy/pull/88) ([mak001](https://github.com/mak001))

## [1.2.1](https://github.com/dynamic/silverstripe-foxy/tree/1.2.1) (2020-06-22)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.2.0...1.2.1)

**Merged pull requests:**

- BUGFIX use Varchar for index field [\#100](https://github.com/dynamic/silverstripe-foxy/pull/100) ([muskie9](https://github.com/muskie9))

## [1.2.0](https://github.com/dynamic/silverstripe-foxy/tree/1.2.0) (2020-06-22)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.1.0...1.2.0)

**Implemented enhancements:**

- FEATURE Product Variations using has\_many relation type [\#97](https://github.com/dynamic/silverstripe-foxy/issues/97)

**Fixed bugs:**

- BUG Product validation is not working [\#66](https://github.com/dynamic/silverstripe-foxy/issues/66)
- BUG Shippable - validation for weight fails even if value \> 0 [\#58](https://github.com/dynamic/silverstripe-foxy/issues/58)

**Closed issues:**

- FEATURE CMSRequiredFIelds\(\) - implement until Validation bug is fixed [\#82](https://github.com/dynamic/silverstripe-foxy/issues/82)

**Merged pull requests:**

- ENHANCEMENT Product Variations by HasMany [\#99](https://github.com/dynamic/silverstripe-foxy/pull/99) ([muskie9](https://github.com/muskie9))
- BUGFIX tests - $required\_extensions [\#96](https://github.com/dynamic/silverstripe-foxy/pull/96) ([jsirish](https://github.com/jsirish))
- BUGFIX Purchasable validation fix [\#95](https://github.com/dynamic/silverstripe-foxy/pull/95) ([jsirish](https://github.com/jsirish))

## [1.1.0](https://github.com/dynamic/silverstripe-foxy/tree/1.1.0) (2020-04-09)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.0.4...1.1.0)

**Closed issues:**

- purchasable items should trim codes and title before saving [\#77](https://github.com/dynamic/silverstripe-foxy/issues/77)

**Merged pull requests:**

- ENHANCEMENT QuantityMax to restrict quantity of product in cart [\#93](https://github.com/dynamic/silverstripe-foxy/pull/93) ([muskie9](https://github.com/muskie9))

## [1.0.4](https://github.com/dynamic/silverstripe-foxy/tree/1.0.4) (2020-03-30)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.0.3...1.0.4)

**Fixed bugs:**

- BUG AddtoCartForm has issues when displaying on VirtualPage [\#91](https://github.com/dynamic/silverstripe-foxy/issues/91)

**Closed issues:**

- FEATURE - Add db and relation fields to class doc blocks [\#86](https://github.com/dynamic/silverstripe-foxy/issues/86)
- BUG - product codes should be trimmed on save [\#89](https://github.com/dynamic/silverstripe-foxy/issues/89)

**Merged pull requests:**

- BUGFIX reset $product to VirtualPage’s page it’s based on [\#92](https://github.com/dynamic/silverstripe-foxy/pull/92) ([muskie9](https://github.com/muskie9))
- Added code trimming for product and option codes [\#90](https://github.com/dynamic/silverstripe-foxy/pull/90) ([mak001](https://github.com/mak001))

## [1.0.3](https://github.com/dynamic/silverstripe-foxy/tree/1.0.3) (2019-12-11)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.0.2...1.0.3)

**Merged pull requests:**

- quantity field in cart now pings for quantity checks [\#85](https://github.com/dynamic/silverstripe-foxy/pull/85) ([mak001](https://github.com/mak001))

## [1.0.2](https://github.com/dynamic/silverstripe-foxy/tree/1.0.2) (2019-12-06)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.0.1...1.0.2)

**Merged pull requests:**

- BUGFIX Add fields for FoxyCategory management [\#84](https://github.com/dynamic/silverstripe-foxy/pull/84) ([jsirish](https://github.com/jsirish))

## [1.0.1](https://github.com/dynamic/silverstripe-foxy/tree/1.0.1) (2019-11-12)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/1.0.0...1.0.1)

**Merged pull requests:**

- CMS Product CMS design [\#81](https://github.com/dynamic/silverstripe-foxy/pull/81) ([jsirish](https://github.com/jsirish))
- BUGFIX Travis PHPCS tests [\#80](https://github.com/dynamic/silverstripe-foxy/pull/80) ([jsirish](https://github.com/jsirish))

## [1.0.0](https://github.com/dynamic/silverstripe-foxy/tree/1.0.0) (2019-10-30)

[Full Changelog](https://github.com/dynamic/silverstripe-foxy/compare/ec5d269858b4774277fd21c60e5ce76023730e6f...1.0.0)

**Implemented enhancements:**

- FEATURE PageController - boolean option to include cart JS  [\#67](https://github.com/dynamic/silverstripe-foxy/issues/67)
- FEATURE FoxyHelper - Set Product Classes [\#62](https://github.com/dynamic/silverstripe-foxy/issues/62)
- REFACTOR - ProductOption - belongs\_many\_many Product [\#52](https://github.com/dynamic/silverstripe-foxy/issues/52)
- REFACTOR - migrate DataTestController route [\#48](https://github.com/dynamic/silverstripe-foxy/issues/48)
- ProductOption - implement Purchasable Permissions [\#40](https://github.com/dynamic/silverstripe-foxy/issues/40)
- OptionType - implement Purchasable Permissions [\#39](https://github.com/dynamic/silverstripe-foxy/issues/39)
- FoxyCategory - implement Purchasable Permissions [\#38](https://github.com/dynamic/silverstripe-foxy/issues/38)
- Purchasable - add test for validate\(\) [\#14](https://github.com/dynamic/silverstripe-foxy/issues/14)
- NEW ProductOptions [\#10](https://github.com/dynamic/silverstripe-foxy/issues/10)
- REQUIRE foxycart.cart\_validation.php [\#9](https://github.com/dynamic/silverstripe-foxy/issues/9)
- NEW FoxyAdmin [\#8](https://github.com/dynamic/silverstripe-foxy/issues/8)
- NEW FoxySettings [\#7](https://github.com/dynamic/silverstripe-foxy/issues/7)
- NEW Shippable DataExtension [\#2](https://github.com/dynamic/silverstripe-foxy/issues/2)
- NEW Purchasable DataExtension [\#1](https://github.com/dynamic/silverstripe-foxy/issues/1)
- NEW AddToCartForm [\#5](https://github.com/dynamic/silverstripe-foxy/issues/5)

**Fixed bugs:**

- BUG FoxyHelper::getProductClasses doesn't always return array [\#64](https://github.com/dynamic/silverstripe-foxy/issues/64)
- Shippable - set default weight to value greater than 0.00 [\#50](https://github.com/dynamic/silverstripe-foxy/issues/50)
- FoxyAdmin - $required\_permission\_codes should be set to EDIT\_FOXY\_SETTING [\#42](https://github.com/dynamic/silverstripe-foxy/issues/42)
- BUG Price Update js not updating the price [\#34](https://github.com/dynamic/silverstripe-foxy/issues/34)
- BUG Product Option availability detection not setup [\#33](https://github.com/dynamic/silverstripe-foxy/issues/33)
- Purchasable - field label for FoxyCategoryID is not picking up [\#19](https://github.com/dynamic/silverstripe-foxy/issues/19)
- Shippable - validate\(\) method ignores Purchasable validate\(\) [\#17](https://github.com/dynamic/silverstripe-foxy/issues/17)

**Closed issues:**

- REFACTOR Shippable to no longer extends Purchasable [\#60](https://github.com/dynamic/silverstripe-foxy/issues/60)
- NEW implement PermissionProvider [\#27](https://github.com/dynamic/silverstripe-foxy/issues/27)
- ProductOption - set Available to true on record create [\#23](https://github.com/dynamic/silverstripe-foxy/issues/23)
- QuantityField - implement css/js [\#22](https://github.com/dynamic/silverstripe-foxy/issues/22)
- NEW FoxyCategory [\#16](https://github.com/dynamic/silverstripe-foxy/issues/16)
- REFACTOR weight field to 3 decimal place [\#75](https://github.com/dynamic/silverstripe-foxy/issues/75)

**Merged pull requests:**

- Weight fields are now three decimal places [\#76](https://github.com/dynamic/silverstripe-foxy/pull/76) ([mak001](https://github.com/mak001))
- FEATURE Weight - allow up to 6 decimals [\#74](https://github.com/dynamic/silverstripe-foxy/pull/74) ([jsirish](https://github.com/jsirish))
- REFACTOR remove unused productID in custom script [\#73](https://github.com/dynamic/silverstripe-foxy/pull/73) ([muskie9](https://github.com/muskie9))
- ENHANCEMENT ProductOption::getPrice\(\) [\#72](https://github.com/dynamic/silverstripe-foxy/pull/72) ([muskie9](https://github.com/muskie9))
- REFACTOR simplify price modification script [\#71](https://github.com/dynamic/silverstripe-foxy/pull/71) ([muskie9](https://github.com/muskie9))
- BUGFIX Purchasable - FoxyCategoryID label [\#70](https://github.com/dynamic/silverstripe-foxy/pull/70) ([jsirish](https://github.com/jsirish))
- FEATURE README badges [\#69](https://github.com/dynamic/silverstripe-foxy/pull/69) ([jsirish](https://github.com/jsirish))
- FEATURE Setting - add option for Sidecart [\#68](https://github.com/dynamic/silverstripe-foxy/pull/68) ([jsirish](https://github.com/jsirish))
-  BUGFIX getFoxyProductClasses returns FoxyHelper in some cases [\#65](https://github.com/dynamic/silverstripe-foxy/pull/65) ([muskie9](https://github.com/muskie9))
- FEATURE FoxyHelper - set product classes for product related queries [\#63](https://github.com/dynamic/silverstripe-foxy/pull/63) ([jsirish](https://github.com/jsirish))
- REFACTOR Shippable no longer extends Purchasable [\#61](https://github.com/dynamic/silverstripe-foxy/pull/61) ([jsirish](https://github.com/jsirish))
- FEATURE Shippable - updateFieldLabels\(\) [\#59](https://github.com/dynamic/silverstripe-foxy/pull/59) ([jsirish](https://github.com/jsirish))
- Shippable - remove weight validation due to bugs [\#57](https://github.com/dynamic/silverstripe-foxy/pull/57) ([jsirish](https://github.com/jsirish))
- new GitHub issue templates [\#56](https://github.com/dynamic/silverstripe-foxy/pull/56) ([jsirish](https://github.com/jsirish))
- bugfix - check for Options prior to including JS [\#55](https://github.com/dynamic/silverstripe-foxy/pull/55) ([jsirish](https://github.com/jsirish))
- composer - add author info [\#54](https://github.com/dynamic/silverstripe-foxy/pull/54) ([jsirish](https://github.com/jsirish))
- refactor - Product many\_many Options [\#53](https://github.com/dynamic/silverstripe-foxy/pull/53) ([jsirish](https://github.com/jsirish))
- bugfix - Shippable - set default weight to value greater than 0 [\#51](https://github.com/dynamic/silverstripe-foxy/pull/51) ([jsirish](https://github.com/jsirish))
- refactor - migrate DataTestController route [\#49](https://github.com/dynamic/silverstripe-foxy/pull/49) ([jsirish](https://github.com/jsirish))
- update docs and guidelines \(\#45\) [\#47](https://github.com/dynamic/silverstripe-foxy/pull/47) ([jsirish](https://github.com/jsirish))
- update docs and guidelines [\#45](https://github.com/dynamic/silverstripe-foxy/pull/45) ([jsirish](https://github.com/jsirish))
- Foxy objects - implement permissions [\#44](https://github.com/dynamic/silverstripe-foxy/pull/44) ([jsirish](https://github.com/jsirish))
- FoxyAdmin - grant access if EDIT\_FOXY\_SETTING [\#43](https://github.com/dynamic/silverstripe-foxy/pull/43) ([jsirish](https://github.com/jsirish))
- Purchasable - initial PermissionProvider [\#41](https://github.com/dynamic/silverstripe-foxy/pull/41) ([jsirish](https://github.com/jsirish))
- Tests/jenkins [\#37](https://github.com/dynamic/silverstripe-foxy/pull/37) ([jsirish](https://github.com/jsirish))
- AddToCartForm - js, template work [\#35](https://github.com/dynamic/silverstripe-foxy/pull/35) ([jsirish](https://github.com/jsirish))
- QuantityField initial migration [\#32](https://github.com/dynamic/silverstripe-foxy/pull/32) ([jsirish](https://github.com/jsirish))
- front end workflow migration [\#31](https://github.com/dynamic/silverstripe-foxy/pull/31) ([jsirish](https://github.com/jsirish))
- FoxyHelper - additional private vars, updated store getters [\#30](https://github.com/dynamic/silverstripe-foxy/pull/30) ([jsirish](https://github.com/jsirish))
- ProductOption - update $summary\_fields [\#29](https://github.com/dynamic/silverstripe-foxy/pull/29) ([jsirish](https://github.com/jsirish))
- coverage [\#26](https://github.com/dynamic/silverstripe-foxy/pull/26) ([jsirish](https://github.com/jsirish))
- ProductOption - set Available to true on create [\#25](https://github.com/dynamic/silverstripe-foxy/pull/25) ([jsirish](https://github.com/jsirish))
- ProductOption - initial build [\#21](https://github.com/dynamic/silverstripe-foxy/pull/21) ([jsirish](https://github.com/jsirish))
- Add to Cart Form - initial build [\#20](https://github.com/dynamic/silverstripe-foxy/pull/20) ([jsirish](https://github.com/jsirish))
- FoxyCategory - initial build [\#18](https://github.com/dynamic/silverstripe-foxy/pull/18) ([jsirish](https://github.com/jsirish))
- Shippable initial build [\#13](https://github.com/dynamic/silverstripe-foxy/pull/13) ([jsirish](https://github.com/jsirish))
- Created basic settings object [\#12](https://github.com/dynamic/silverstripe-foxy/pull/12) ([mak001](https://github.com/mak001))
- Purchasable - initial build [\#11](https://github.com/dynamic/silverstripe-foxy/pull/11) ([jsirish](https://github.com/jsirish))



\* *This Changelog was automatically generated by [github_changelog_generator](https://github.com/github-changelog-generator/github-changelog-generator)*
