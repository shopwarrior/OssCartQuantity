<?php

class Shopware_Plugins_Frontend_OssCartQuantity_Bootstrap extends Shopware_Components_Plugin_Bootstrap {
    /**
     * Helper for availability capabilities
     * @return array
     */
    public function getCapabilities(){
        return array(
            'install' => true,
            'update' => true,
            'enable' => true,
        );
    }

    /**
     * Returns the meta information about the plugin.
     *
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'author' => 'Odessite',
            'supplier' => 'Odessite',
            'label' => $this->getLabel(),
            'description' => file_get_contents(__DIR__ . '/description.html'),
            'copyright' => 'Copyright &copy; '.date('Y').', Odessite',
            'support' => 'info@shopwarrior.net',
            'link' => 'http://odessite.com.ua/'
        );
    }

    /**
     * Returns the version of plugin as string.
     *
     * @return string
     */
    public function getVersion() {
        return '0.0.1';
    }

    /**
     * Returns the plugin name for backend
     *
     * @return string
     */
    public function getLabel() {
        return 'Mini Cart Total Quantity';
    }

    /**
     * Standard plugin install method to register all required components.
     * @return array
     */
    public function install() {
        try {
            $this->subscribeEvents();
        } catch(Exception $e) {
            return array(
                'success' => false,
                'message' => $e->getMessage(),
                'invalidateCache' => $this->getInvalidateCacheArray()
            );
        }

        return array(
            'success' => true,
            'message' => 'Plugin was successfully installed',
            'invalidateCache' => $this->getInvalidateCacheArray()
        );
    }

    public function totalCountBasket(\Enlight_Hook_HookArgs $args){
//        Items quantity summ
        $count = Shopware()->Db()->fetchOne(
            'SELECT SUM(`quantity`) FROM s_order_basket WHERE modus = 0 AND sessionID = ?',
            array(Shopware()->SessionID()));

        $count = empty($count)? '0': (string)$count;

        $args->setReturn($count);
        return $count;
    }

    /**
     * @return Shopware_Plugins_Frontend_OssCartQuantity_Bootstrap
     */
    private function subscribeEvents(){
        $this->subscribeEvent(
            'sBasket::sCountBasket::replace', 'totalCountBasket'
        );

        return $this;
    }

    /**
     * Helper for cache array
     * @return array
     */
    private function getInvalidateCacheArray()
    {
        return array('config', 'template', 'theme');
    }
}
