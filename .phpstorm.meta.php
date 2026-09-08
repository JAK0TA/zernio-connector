<?php

/**
 * Extend PhpStorms code completion capabilities by providing a meta file.
 *
 * Kudos to Alexander Schnitzler's work, see https://github.com/alexanderschnitzler/phpstorm.meta.php-typo3
 *
 * @see https://www.jetbrains.com/help/phpstorm/ide-advanced-metadata.html
 */

namespace PHPSTORM_META;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Backend\Module\Module;
use TYPO3\CMS\Backend\Module\ModuleData;
use TYPO3\CMS\Backend\Module\ModuleInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\DateTimeAspect;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Context\TypoScriptAspect;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Context\VisibilityAspect;
use TYPO3\CMS\Core\Context\WorkspaceAspect;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\PageRouter;
use TYPO3\CMS\Core\Routing\RouteResultInterface;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

// valid for all TYPO3 versions
override(GeneralUtility::makeInstance(0, 1), map(['' => '@']));

// valid for all TYPO3 versions
override(ObjectManager::get(0, 1), map(['' => '@']));
override(ObjectManagerInterface::get(0, 1), map(['' => '@']));

// Contexts
// @see https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/9.4/Feature-85389-ContextAPIForConsistentDataHandling.html
expectedArguments(
  Context::getAspect(),
  0,
  'date',
  'visibility',
  'backend.user',
  'frontend.user',
  'workspace',
  'language',
  'typoscript'
);

override(Context::getAspect(), map([
  'date' => DateTimeAspect::class,
  'visibility' => VisibilityAspect::class,
  'backend.user' => UserAspect::class,
  'frontend.user' => UserAspect::class,
  'workspace' => WorkspaceAspect::class,
  'language' => LanguageAspect::class,
  'typoscript' => TypoScriptAspect::class,
]));

expectedArguments(
  DateTimeAspect::get(),
  0,
  'timestamp',
  'iso',
  'timezone',
  'full',
  'accessTime'
);

expectedArguments(
  VisibilityAspect::get(),
  0,
  'includeHiddenPages',
  'includeHiddenContent',
  'includeDeletedRecords'
);

expectedArguments(
  UserAspect::get(),
  0,
  'id',
  'username',
  'isLoggedIn',
  'isAdmin',
  'groupIds',
  'groupNames'
);

expectedArguments(
  WorkspaceAspect::get(),
  0,
  'id',
  'isLive',
  'isOffline'
);

expectedArguments(
  LanguageAspect::get(),
  0,
  'id',
  'contentId',
  'fallbackChain',
  'overlayType',
  'legacyLanguageMode',
  'legacyOverlayType'
);

expectedArguments(
  TypoScriptAspect::get(),
  0,
  'forcedTemplateParsing'
);

expectedArguments(
  ServerRequestInterface::getAttribute(),
  0,
  'backend.user',
  'frontend.user',
  'normalizedParams',
  'site',
  'language',
  'routing',
  'module',
  'moduleData'
);

override(ServerRequestInterface::getAttribute(), map([
  'backend.user' => FrontendBackendUserAuthentication::class,
  'frontend.user' => FrontendUserAuthentication::class,
  'normalizedParams' => NormalizedParams::class,
  'site' => SiteInterface::class,
  'language' => SiteLanguage::class,
  'routing' => '\TYPO3\CMS\Core\Routing\SiteRouteResult|\TYPO3\CMS\Core\Routing\PageArguments',
  'module' => ModuleInterface::class,
  'moduleData' => ModuleData::class,
]));

expectedArguments(
  ServerRequest::getAttribute(),
  0,
  'backend.user',
  'frontend.user',
  'normalizedParams',
  'site',
  'language',
  'routing',
  'module',
  'moduleData'
);

override(ServerRequest::getAttribute(), map([
  'backend.user' => FrontendBackendUserAuthentication::class,
  'frontend.user' => FrontendUserAuthentication::class,
  'normalizedParams' => NormalizedParams::class,
  'site' => Site::class,
  'language' => SiteLanguage::class,
  'routing' => '\TYPO3\CMS\Core\Routing\SiteRouteResult|\TYPO3\CMS\Core\Routing\PageArguments',
  'module' => Module::class,
  'moduleData' => ModuleData::class,
]));

override(Request::getAttribute(), map([
  'backend.user' => FrontendBackendUserAuthentication::class,
  'frontend.user' => FrontendUserAuthentication::class,
  'frontend.controller' => TypoScriptFrontendController::class,
  'normalizedParams' => NormalizedParams::class,
  'site' => Site::class,
  'language' => SiteLanguage::class,
  'routing' => '\TYPO3\CMS\Core\Routing\SiteRouteResult|\TYPO3\CMS\Core\Routing\PageArguments',
  'module' => Module::class,
  'moduleData' => ModuleData::class,
]));

override(
  SiteMatcher::matchRequest(),
  type(
    SiteRouteResult::class,
    RouteResultInterface::class,
  )
);

override(PageRouter::matchRequest(), type(
  PageArguments::class,
  RouteResultInterface::class,
));
