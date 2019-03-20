<?php
class SYIAD_Options {

    private $options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    public function add_plugin_page() {
        add_options_page(
            __('SaveYourInternet Protest Page', 'saveyourinternet-protest-page'),
            __('SaveYourInternet Protest Page', 'saveyourinternet-protest-page'),
            'switch_themes',
            'syiad-admin',
            array( $this, 'create_admin_page' )
        );
    }

    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'syiad_option' );
        ?>
        <div class="wrap">
            <h1><?php _e("SaveYourInternet Protest Page", 'saveyourinternet-protest-page');?></h1>
            <form method="post" action="options.php">
            <?php
                settings_fields( 'syiad_optiongroup' );
                do_settings_sections( 'syiad-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'syiad_optiongroup',
            'syiad_option',
            array( $this, 'sanitize' )
        );

        add_settings_section(
            'syiad_option_section_general',
            __('main settings', 'saveyourinternet-protest-page'),
            array( $this, 'section_info_general' ),
            'syiad-admin'
        );

        add_settings_field(
            'enable',
            __('activate protest', 'saveyourinternet-protest-page'),
            array( $this, 'general_enable_callback' ),
            'syiad-admin',
            'syiad_option_section_general'
        );
        add_settings_field(
            'type',
            __('info box not closable', 'saveyourinternet-protest-page'),
            array( $this, 'general_notclosable_callback' ),
            'syiad-admin',
            'syiad_option_section_general'
        );
        add_settings_field(
            'date',
            __('date', 'saveyourinternet-protest-page'),
            array( $this, 'general_date_callback' ),
            'syiad-admin',
            'syiad_option_section_general'
        );
        add_settings_field(
            'exclude',
            __('exclude', 'saveyourinternet-protest-page'),
            array( $this, 'general_exclude_callback' ),
            'syiad-admin',
            'syiad_option_section_general'
        );

        add_settings_section(
            'syiad_option_section_debug',
            __('debug', 'saveyourinternet-protest-page'),
            array( $this, 'section_info_debug' ),
            'syiad-admin'
        );
        add_settings_field(
            'testmode',
            __('test mode', 'saveyourinternet-protest-page'),
            array( $this, 'debug_testmode_callback' ),
            'syiad-admin',
            'syiad_option_section_debug'
        );

        add_settings_section(
            'syiad_option_section_format',
            __('format', 'saveyourinternet-protest-page'),
            array( $this, 'section_info_format' ),
            'syiad-admin'
        );
        add_settings_field(
            'link',
            __('link URL', 'saveyourinternet-protest-page'),
            array( $this, 'format_link_callback' ),
            'syiad-admin',
            'syiad_option_section_format'
        );
        add_settings_field(
            'customtext',
            __('use custom text', 'saveyourinternet-protest-page'),
            array( $this, 'format_customtext_callback' ),
            'syiad-admin',
            'syiad_option_section_format'
        );
        add_settings_field(
            'text',
            __('custom text', 'saveyourinternet-protest-page'),
            array( $this, 'format_text_callback' ),
            'syiad-admin',
            'syiad_option_section_format'
        );
        add_settings_field(
            'showlogo',
            __('show logo', 'saveyourinternet-protest-page'),
            array( $this, 'format_showlogo_callback' ),
            'syiad-admin',
            'syiad_option_section_format'
        );
        add_settings_field(
            'impressum',
            __('link to impressum', 'saveyourinternet-protest-page'),
            array( $this, 'format_impressum_callback' ),
            'syiad-admin',
            'syiad_option_section_format'
        );
        add_settings_field(
            'privacy',
            __('link to provacy policy', 'saveyourinternet-protest-page'),
            array( $this, 'format_privacy_callback' ),
            'syiad-admin',
            'syiad_option_section_format'
        );


    }

    public function sanitize( $input ) {
        $new_input = array();

        if( isset( $input['general']['enable'] ) && ( "1" == $input['general']['enable'] ))
            $new_input['general']['enable'] = true;
        else
            $new_input['general']['enable'] = false;

        if( isset( $input['general']['notclosable'] ) && ( "1" == $input['general']['notclosable'] ))
            $new_input['general']['notclosable'] = true;
        else
            $new_input['general']['notclosable'] = false;

        if( isset( $input['general']['date'] ) && (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $input['general']['date']) )) {
            $new_input['general']['date'] = $input['general']['date'];
        } else {
            $new_input['general']['date'] = '2019-03-21';
        }

        if( isset( $input['general']['exclude'] ) && (preg_match('/^[0-9]*(?:,[0-9]+,?)*$/', $input['general']['exclude']) )) {
            $new_input['general']['exclude'] = $input['general']['exclude'];
        } else {
            $new_input['general']['exclude'] = '';
        }

        if( isset( $input['debug']['testmode'] ) && ( "1" == $input['debug']['testmode'] ))
            $new_input['debug']['testmode'] = true;
        else
            $new_input['debug']['testmode'] = false;

        if( isset( $input['format']['link'] ) && ( "" != trim($input['format']['link']) ))
            $new_input['format']['link'] = sanitize_text_field($input['format']['link']);
        else
            $new_input['format']['link'] = SYIAD_DEFAULTLINK;

        if( isset( $input['format']['customtext'] ) && ( "1" == $input['format']['customtext'] ))
            $new_input['format']['customtext'] = true;
        else
            $new_input['format']['customtext'] = false;

        if( isset( $input['format']['text'] ) )
            $new_input['format']['text'] = wp_kses_post($input['format']['text']);

        if( isset( $input['format']['showlogo'] ) && ( "1" == $input['format']['showlogo'] ))
            $new_input['format']['showlogo'] = true;
        else
            $new_input['format']['showlogo'] = false;

        if( isset( $input['format']['impressum'] ) && ( "" != trim($input['format']['impressum']) ))
            $new_input['format']['impressum'] = sanitize_text_field($input['format']['impressum']);
        else
            $new_input['format']['impressum'] = "";

        if( isset( $input['format']['privacy'] ) && ( "" != trim($input['format']['privacy']) ))
            $new_input['format']['privacy'] = sanitize_text_field($input['format']['privacy']);
        else
            $new_input['format']['privacy'] = "";

        return $new_input;
    }

    public function section_info_general() {

    }

    public function section_info_debug() {

    }

    public function section_info_format() {

    }


    public function general_enable_callback() {
        echo '<input type="checkbox" id="general_enable" name="syiad_option[general][enable]" value="1" '. checked( $this->options['general']['enable'], true, false ) .' />';
        echo '<label for="general_enable">'.__('activate', 'saveyourinternet-protest-page').'</label>';
        echo '<p class="description">'.__('A protest box will be shown on your site on the chosen date', 'saveyourinternet-protest-page').'</p>';
    }
    public function general_notclosable_callback() {
        echo '<input type="checkbox" id="general_notclosable" name="syiad_option[general][notclosable]" value="1" '. checked( $this->options['general']['notclosable'], true, false ) .' />';
        echo '<label for="general_notclosable">'.__('activate', 'saveyourinternet-protest-page').'</label>';
        echo '<p class="description">'.__('If activated, the box will not be closable. Your site will be unusable.', 'saveyourinternet-protest-page').'</p>';
    }
    public function general_date_callback() {
        printf('<input type="date" id="general_date" name="syiad_option[general][date]" value="%s" />',
        isset( $this->options['general']['date'] ) ? esc_attr( $this->options['general']['date']) : '2019-03-21' );
        echo '<p class="description">'.__('Online protest day is March 21st. Offline protests will be on march 23rd.', 'saveyourinternet-protest-page').'</p>';
    }
    public function general_exclude_callback() {
        printf('<input type="text" id="general_exclude" name="syiad_option[general][exclude]" value="%s" />',
        isset( $this->options['general']['exclude'] ) ? esc_attr( $this->options['general']['exclude']) : '' );
        echo '<p class="description">'.__('comma-seperated list wirh page/post-ids, which will be excluded.', 'saveyourinternet-protest-page').'</p>';
    }
    public function debug_testmode_callback() {
        echo '<input type="checkbox" id="debug_testmode" name="syiad_option[debug][testmode]" value="1" '. checked( $this->options['debug']['testmode'], true, false ) .' />';
        echo '<label for="debug_testmode">'.__('activate', 'saveyourinternet-protest-page').'</label>';
        echo '<p class="description">'.__('Show overlay now.', 'saveyourinternet-protest-page').'</p>';
    }
    public function format_link_callback() {
        printf('<input type="text" id="format_link" name="syiad_option[format][link]" value="%s" />',
        isset( $this->options['format']['link'] ) ? esc_attr( $this->options['format']['link']) : SYIAD_DEFAULTLINK );
    }
    public function format_customtext_callback() {
        echo '<input type="checkbox" id="format_customtext" name="syiad_option[format][customtext]" value="1" '. checked( $this->options['format']['customtext'], true, false ) .' />';
        echo '<label for="format_customtext">'.__('activate', 'saveyourinternet-protest-page').'</label>';
    }
    public function format_text_callback() {
        printf('<textarea id="format_text" name="syiad_option[format][text]">%s</textarea>',
        isset( $this->options['format']['text'] ) ? esc_textarea( $this->options['format']['text']) : "" );
    }
    public function format_showlogo_callback() {
        echo '<input type="checkbox" id="format_showlogo" name="syiad_option[format][showlogo]" value="1" ';
        if ( !isset($this->options['format']['showlogo']) || (true === $this->options['format']['showlogo'])) echo 'checked="checked"';
        echo ' />';
        echo '<label for="format_showlogo">'.__('activate', 'saveyourinternet-protest-page').'</label>';
    }
    public function format_impressum_callback() {
        printf('<input type="text" id="format_impressum" name="syiad_option[format][impressum]" value="%s" />',
        isset( $this->options['format']['impressum'] ) ? esc_attr( $this->options['format']['impressum']) : "" );
    }
    public function format_privacy_callback() {
        printf('<input type="text" id="format_privacy" name="syiad_option[format][privacy]" value="%s" />',
        isset( $this->options['format']['privacy'] ) ? esc_attr( $this->options['format']['privacy']) : "" );
        echo '<p class="description">'.__('remember to exclude your impressum and privacy policy page', 'saveyourinternet-protest-page').'</p>';
    }
}

if( is_admin() )
    $SYIAD_settingspage = new SYIAD_Options();
