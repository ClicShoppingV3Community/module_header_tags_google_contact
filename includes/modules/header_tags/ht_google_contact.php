<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\CLICSHOPPING;

  class ht_google_contact
  {
    public $code;
    public $group;
    public string $title;
    public string $description;
    public ?int $sort_order = 0;
    public bool $enabled = false;

    public function __construct()
    {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_header_tags_google_contact_meta_title');
      $this->description = CLICSHOPPING::getDef('module_header_tags_google_contact_meta_description');

      if (defined('MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_STATUS')) {
        $this->sort_order = MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_SORT_ORDER;
        $this->enabled = (MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_STATUS == 'True');
      }
    }

    public function execute()
    {

      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (!$CLICSHOPPING_Customer->isLoggedOn()) {

        if (!empty(MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_PHONE)) {
          $phone = MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_PHONE;

          $footer_tag = '<!--  google phone start -->' . "\n";

          $footer_tag .= '
  <script type="application/ld+json">
{
  "@context": "http://schema.org",
   "@type": "Organization",
   "name": "' . STORE_NAME . '",
   "legalName" : "' . STORE_NAME . '",
   "url": "' . HTTP::typeUrlDomain() . '",
   "contactPoint": [{
                   "@type": "ContactPoint",
                   "contactType": "customer support",
                   "telephone": "[+561-526-8457]",
                    }
                  ]
}

    ' . "\n";

          $footer_tag .= '</script>' . "\n";

          $footer_tag .= '<!-- google phone end -->' . "\n";

          $CLICSHOPPING_Template->addBlock($footer_tag, 'footer_scripts');
        }
      }
    }

    public function isEnabled()
    {
      return $this->enabled;
    }

    public function check()
    {
      return defined('MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_STATUS');
    }

    public function install()
    {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Do you want to install this module ?',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Do you want to install this module ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez indiquer votre téléphone',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_PHONE',
          'configuration_value' => '',
          'configuration_description' => 'Veuillez insérer votre téléphone support client',
          'configuration_group_id' => '6',
          'sort_order' => '2',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Sort Order',
          'configuration_key' => 'MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_SORT_ORDER',
          'configuration_value' => '95',
          'configuration_description' => 'Sort order. Lowest is displayed in first',
          'configuration_group_id' => '6',
          'sort_order' => '75',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
        ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
      );
    }

    public function remove()
    {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys()
    {
      return array('MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_STATUS',
        'MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_PHONE',
        'MODULE_HEADER_TAGS_GOOGLE_CONTACT_META_SORT_ORDER'
      );
    }
  }

?>