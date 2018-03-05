<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 08:11
 */

namespace Jinya\Services\Theme;


use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Theme;
use Jinya\Services\Menu\MenuServiceInterface;
use Symfony\Component\Yaml\Yaml;
use function array_replace;
use function array_replace_recursive;

class ThemeConfigService implements ThemeConfigServiceInterface
{

    /** @var ThemeServiceInterface */
    private $themeService;
    /** @var MenuServiceInterface */
    private $menuService;
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ThemeConfigService constructor.
     * @param ThemeServiceInterface $themeService
     * @param MenuServiceInterface $menuService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ThemeServiceInterface $themeService, MenuServiceInterface $menuService, EntityManagerInterface $entityManager)
    {
        $this->themeService = $themeService;
        $this->menuService = $menuService;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritdoc
     */
    public function saveConfig(string $themeName, array $config): void
    {
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeConfig = $this->getThemeConfig($theme->getName());

        $targetConfig = array_replace_recursive($theme->getConfiguration(), $config);

        if (array_key_exists('defaultConfig', $themeConfig)) {
            $defaultConfig = $themeConfig['defaultConfig'];
            if (is_array($defaultConfig)) {
                $theme->setConfiguration(array_replace_recursive($defaultConfig, $targetConfig));
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getThemeConfig(string $name): array
    {
        $theme = $this->themeService->getTheme($name);

        return Yaml::parse(file_get_contents($this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML));
    }

    /**
     * @inheritdoc
     */
    public function getThemeNamespace(Theme $theme): string
    {
        return '@Themes/' . $theme->getName();
    }

    /**
     * @inheritdoc
     */
    public function getConfigForm(string $name): array
    {
        return $this->getForms($name)['config'];
    }

    /**
     * @inheritdoc
     */
    public function getForms(string $name): array
    {
        $themeYml = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML;

        $themeData = Yaml::parseFile($themeYml);

        return $themeData['form'];
    }

    /**
     * @inheritdoc
     */
    public function getVariablesForm(string $name): array
    {
        $theme = $this->themeService->getTheme($name);
        $stylesPath = $this->getStylesPath($theme);
        $themeConfig = $this->getThemeConfig($theme->getName());

        $variablesPath = $stylesPath . DIRECTORY_SEPARATOR . $themeConfig['styles']['variables']['file'];
        $handle = fopen($variablesPath, "r");
        $variables = [];

        try {
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/^\$.*!default;\s$/', $line)) {
                        $replaced = preg_replace('/ !default;$/', '', $line);
                        $variables[] = explode(':', $replaced);
                    }
                }
            }
        } finally {
            fclose($handle);
        }

        return $variables;
    }

    /**
     * @inheritdoc
     */
    public function getStylesPath(Theme $theme): string
    {
        $themeConfig = $this->getThemeConfig($theme->getName());
        $stylesBasePath = 'public/scss/';
        if (array_key_exists('styles_base', $themeConfig)) {
            $stylesBasePath = $themeConfig['styles_base'];
        }

        $stylesPath = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . $stylesBasePath;

        return $stylesPath;
    }

    /**
     * @inheritdoc
     */
    public function setVariables(string $name, array $variables): void
    {
        $theme = $this->themeService->getTheme($name);
        $theme->setScssVariables(array_replace($theme->getScssVariables(), array_filter($variables)));
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function setMenus(string $name, array $menus): void
    {
        $theme = $this->themeService->getTheme($name);

        $primaryMenu = $menus['primary'];
        $secondaryMenu = $menus['secondary'];
        $footerMenu = $menus['footer'];

        if ($primaryMenu) {
            $theme->setPrimaryMenu($this->menuService->get($primaryMenu));
        }
        if ($secondaryMenu) {
            $theme->setSecondaryMenu($this->menuService->get($secondaryMenu));
        }
        if ($footerMenu) {
            $theme->setFooterMenu($this->menuService->get($footerMenu));
        }

        $this->entityManager->flush();
    }

    /**
     * Resets the given themes configuration
     *
     * @param string $name
     */
    public function resetConfig(string $name): void
    {
        $theme = $this->themeService->getThemeOrNewTheme($name);
        $theme->setConfiguration([]);
        $this->entityManager->flush();
    }

    /**
     * Resets the given themes variables
     *
     * @param string $name
     */
    public function resetVariables(string $name): void
    {
        $theme = $this->themeService->getThemeOrNewTheme($name);
        $theme->setScssVariables([]);
        $this->entityManager->flush();
    }
}