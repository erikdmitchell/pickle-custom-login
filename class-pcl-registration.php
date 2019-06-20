<?php
/**
 * Registration class
 *
 * @package PickleCustomLogin
 * @since   1.0.0
 */

/**
 * PCL_Registration class.
 */
class PCL_Registration {

    /**
     * Admin activate account required
     *
     * (default value: false)
     *
     * @var bool
     * @access protected
     */
    protected $admin_activate_account_required = false;

    /**
     * Activate account required
     *
     * (default value: false)
     *
     * @var bool
     * @access protected
     */
    protected $activate_account_required = false;

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct() {
        add_action( 'init', array( $this, 'add_new_user' ) );
        add_action( 'login_form_register', array( $this, 'register_form_redirect' ) );
        add_action( 'pcl_before_register-form', 'pcl_show_error_messages' );

        add_shortcode( 'pcl-registration-form', array( $this, 'registration_form' ) );
    }

    /**
     * Registration form.
     *
     * @access public
     * @return html
     */
    public function registration_form() {
        if ( is_user_logged_in() ) {
            return pcl_get_template_html( 'logged-in' );
        }

        if ( $this->admin_activate_account_required ) :
            echo esc_html( pcl_format_error_message( '', 'You will receive an email when your account is approved by an admin.', 'success' ) );
        elseif ( $this->activate_account_required ) :
            echo esc_html( pcl_format_error_message( '', 'Please check your email to activate your account.', 'success' ) );
        endif;

        return pcl_get_template_html( 'register-form' );
    }

    /**
     * Form username field.
     *
     * @access public
     * @return void
     */
    public function form_username_field() {
        echo sprintf( '<label for="pcl_username" class="required">%s</label>', esc_html__( 'Username', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[username]" id="pcl_username" class="" type="text"/>' );
    }

    /**
     * Form email field.
     *
     * @access public
     * @return void
     */
    public function form_email_field() {
        echo sprintf( '<label for="pcl_email" class="required">%s</label>', esc_html__( 'Email', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[email]" id="pcl_email" class="email" type="email"/>' );
    }

    /**
     * Form name field.
     *
     * @access public
     * @return void
     */
    public function form_name_field() {
        echo sprintf( '<label for="pcl_firstname">%s</label>', esc_html__( 'First Name', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[firstname]" id="pcl_firstname" type="text"/>' );

        echo sprintf( '<label for="pcl_lastname">%s</label>', esc_html__( 'Last Name', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[lastname]" id="pcl_lastname" type="text"/>' );
    }

    /**
     * Form password field.
     *
     * @access public
     * @return void
     */
    public function form_password_field() {
        echo sprintf( '<label for="pcl_password" class="required">%s</label>', esc_html__( 'Password', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[password]" id="pcl_password" class="password" type="password"/>' );

        echo sprintf( '<label for="pcl_password_check" class="required">%s</label>', esc_html__( 'Password Again', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[password_check]" id="pcl_password_check" class="password" type="password"/>' );
    }

    /**
     * Form company field.
     *
     * @access public
     * @return void
     */
    public function form_company_field() {
        echo sprintf( '<label for="pcl_company">%s</label>', esc_html__( 'Company', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[company]" id="pcl_company" type="text" required/>' );
    }

    /**
     * Form title field.
     *
     * @access public
     * @return void
     */
    public function form_title_field() {
        echo sprintf( '<label for="pcl_title" class="required">%s</label>', esc_html__( 'Job Title', 'pcl' ) );
        echo sprintf( '<input name="esc_html__[title]" id="pcl_title" type="text" required/>' );
    }

    /**
     * Form phone field.
     *
     * @access public
     * @return void
     */
    public function form_phone_field() {
        echo sprintf( '<label for="pcl_phone" class="required">%s</label>', esc_html__( 'Phone', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[phone]" id="pcl_phone" type="text" required/>' );
    }

    /**
     * Form country field.
     *
     * @access public
     * @return void
     */
    public function form_country_field() {
        echo sprintf( '<label for="country" class="required">%s</label>', esc_html__( 'Country', 'pcl' ) );
        echo '<select name="pcl_registration[country]" id="country" onchange="printStateMenu(this.value);" required>
                            <option value="">Select country…</option>
                            <option value="US">United States of America</option>
                            <option value="CA">Canada</option>
                            <option value="AF">Afghanistan</option>
                            <option value="AX">Åland</option>
                            <option value="AL">Albania</option>
                            <option value="DZ">Algeria</option>
                            <option value="AS">American Samoa</option>
                            <option value="AD">Andorra</option>
                            <option value="AO">Angola</option>
                            <option value="AI">Anguilla</option>
                            <option value="AQ">Antarctica</option>
                            <option value="AG">Antigua and Barbuda</option>
                            <option value="AR">Argentina</option>
                            <option value="AM">Armenia</option>
                            <option value="AW">Aruba</option>
                            <option value="AU">Australia</option>
                            <option value="AT">Austria</option>
                            <option value="AZ">Azerbaijan</option>
                            <option value="BS">Bahamas</option>
                            <option value="BH">Bahrain</option>
                            <option value="BD">Bangladesh</option>
                            <option value="BB">Barbados</option>
                            <option value="BY">Belarus</option>
                            <option value="BE">Belgium</option>
                            <option value="BZ">Belize</option>
                            <option value="BJ">Benin</option>
                            <option value="BM">Bermuda</option>
                            <option value="BT">Bhutan</option>
                            <option value="BO">Bolivia</option>
                            <option value="BA">Bosnia and Herzegovina</option>
                            <option value="BW">Botswana</option>
                            <option value="BV">Bouvet Island</option>
                            <option value="BR">Brazil</option>
                            <option value="IO">British Indian Ocean Territory</option>
                            <option value="BN">Brunei Darussalam</option>
                            <option value="BG">Bulgaria</option>
                            <option value="BF">Burkina Faso</option>
                            <option value="BI">Burundi</option>
                            <option value="KH">Cambodia</option>
                            <option value="CM">Cameroon</option>
                            <option value="CV">Cape Verde</option>
                            <option value="KY">Cayman Islands</option>
                            <option value="CF">Central African Republic</option>
                            <option value="TD">Chad</option>
                            <option value="CL">Chile</option>
                            <option value="CN">China</option>
                            <option value="CX">Christmas Island</option>
                            <option value="CC">Cocos (Keeling) Islands</option>
                            <option value="CO">Colombia</option>
                            <option value="KM">Comoros</option>
                            <option value="CG">Congo (Brazzaville)</option>
                            <option value="CD">Congo (Kinshasa)</option>
                            <option value="CK">Cook Islands</option>
                            <option value="CR">Costa Rica</option>
                            <option value="CI">Côte d’Ivoire</option>
                            <option value="HR">Croatia</option>
                            <option value="CU">Cuba</option>
                            <option value="CY">Cyprus</option>
                            <option value="CZ">Czech Republic</option>
                            <option value="DK">Denmark</option>
                            <option value="DJ">Djibouti</option>
                            <option value="DM">Dominica</option>
                            <option value="DO">Dominican Republic</option>
                            <option value="EC">Ecuador</option>
                            <option value="EG">Egypt</option>
                            <option value="SV">El Salvador</option>
                            <option value="GQ">Equatorial Guinea</option>
                            <option value="ER">Eritrea</option>
                            <option value="EE">Estonia</option>
                            <option value="ET">Ethiopia</option>
                            <option value="FK">Falkland Islands</option>
                            <option value="FO">Faroe Islands</option>
                            <option value="FJ">Fiji</option>
                            <option value="FI">Finland</option>
                            <option value="FR">France</option>
                            <option value="GF">French Guiana</option>
                            <option value="PF">French Polynesia</option>
                            <option value="TF">French Southern Lands</option>
                            <option value="GA">Gabon</option>
                            <option value="GM">Gambia</option>
                            <option value="GE">Georgia</option>
                            <option value="DE">Germany</option>
                            <option value="GH">Ghana</option>
                            <option value="GI">Gibraltar</option>
                            <option value="GR">Greece</option>
                            <option value="GL">Greenland</option>
                            <option value="GD">Grenada</option>
                            <option value="GP">Guadeloupe</option>
                            <option value="GU">Guam</option>
                            <option value="GT">Guatemala</option>
                            <option value="GG">Guernsey</option>
                            <option value="GN">Guinea</option>
                            <option value="GW">Guinea-Bissau</option>
                            <option value="GY">Guyana</option>
                            <option value="HT">Haiti</option>
                            <option value="HM">Heard and McDonald Islands</option>
                            <option value="HN">Honduras</option>
                            <option value="HK">Hong Kong</option>
                            <option value="HU">Hungary</option>
                            <option value="IS">Iceland</option>
                            <option value="IN">India</option>
                            <option value="ID">Indonesia</option>
                            <option value="IR">Iran</option>
                            <option value="IQ">Iraq</option>
                            <option value="IE">Ireland</option>
                            <option value="IM">Isle of Man</option>
                            <option value="IL">Israel</option>
                            <option value="IT">Italy</option>
                            <option value="JM">Jamaica</option>
                            <option value="JP">Japan</option>
                            <option value="JE">Jersey</option>
                            <option value="JO">Jordan</option>
                            <option value="KZ">Kazakhstan</option>
                            <option value="KE">Kenya</option>
                            <option value="KI">Kiribati</option>
                            <option value="KP">Korea ( North )</option>
                            <option value="KR">Korea ( South )</option>
                            <option value="KW">Kuwait</option>
                            <option value="KG">Kyrgyzstan</option>
                            <option value="LA">Laos</option>
                            <option value="LV">Latvia</option>
                            <option value="LB">Lebanon</option>
                            <option value="LS">Lesotho</option>
                            <option value="LR">Liberia</option>
                            <option value="LY">Libya</option>
                            <option value="LI">Liechtenstein</option>
                            <option value="LT">Lithuania</option>
                            <option value="LU">Luxembourg</option>
                            <option value="MO">Macau</option>
                            <option value="MK">Macedonia</option>
                            <option value="MG">Madagascar</option>
                            <option value="MW">Malawi</option>
                            <option value="MY">Malaysia</option>
                            <option value="MV">Maldives</option>
                            <option value="ML">Mali</option>
                            <option value="MT">Malta</option>
                            <option value="MH">Marshall Islands</option>
                            <option value="MQ">Martinique</option>
                            <option value="MR">Mauritania</option>
                            <option value="MU">Mauritius</option>
                            <option value="YT">Mayotte</option>
                            <option value="MX">Mexico</option>
                            <option value="FM">Micronesia</option>
                            <option value="MD">Moldova</option>
                            <option value="MC">Monaco</option>
                            <option value="MN">Mongolia</option>
                            <option value="ME">Montenegro</option>
                            <option value="MS">Montserrat</option>
                            <option value="MA">Morocco</option>
                            <option value="MZ">Mozambique</option>
                            <option value="MM">Myanmar</option>
                            <option value="NA">Namibia</option>
                            <option value="NR">Nauru</option>
                            <option value="NP">Nepal</option>
                            <option value="NL">Netherlands</option>
                            <option value="AN">Netherlands Antilles</option>
                            <option value="NC">New Caledonia</option>
                            <option value="NZ">New Zealand</option>
                            <option value="NI">Nicaragua</option>
                            <option value="NE">Niger</option>
                            <option value="NG">Nigeria</option>
                            <option value="NU">Niue</option>
                            <option value="NF">Norfolk Island</option>
                            <option value="MP">Northern Mariana Islands</option>
                            <option value="NO">Norway</option>
                            <option value="OM">Oman</option>
                            <option value="PK">Pakistan</option>
                            <option value="PW">Palau</option>
                            <option value="PS">Palestine</option>
                            <option value="PA">Panama</option>
                            <option value="PG">Papua New Guinea</option>
                            <option value="PY">Paraguay</option>
                            <option value="PE">Peru</option>
                            <option value="PH">Philippines</option>
                            <option value="PN">Pitcairn</option>
                            <option value="PL">Poland</option>
                            <option value="PT">Portugal</option>
                            <option value="PR">Puerto Rico</option>
                            <option value="QA">Qatar</option>
                            <option value="RE">Reunion</option>
                            <option value="RO">Romania</option>
                            <option value="RU">Russian Federation</option>
                            <option value="RW">Rwanda</option>
                            <option value="BL">Saint Barthélemy</option>
                            <option value="SH">Saint Helena</option>
                            <option value="KN">Saint Kitts and Nevis</option>
                            <option value="LC">Saint Lucia</option>
                            <option value="MF">Saint Martin (French part)</option>
                            <option value="PM">Saint Pierre and Miquelon</option>
                            <option value="VC">Saint Vincent and the Grenadines</option>
                            <option value="WS">Samoa</option>
                            <option value="SM">San Marino</option>
                            <option value="ST">Sao Tome and Principe</option>
                            <option value="SA">Saudi Arabia</option>
                            <option value="SN">Senegal</option>
                            <option value="RS">Serbia</option>
                            <option value="SC">Seychelles</option>
                            <option value="SL">Sierra Leone</option>
                            <option value="SG">Singapore</option>
                            <option value="SK">Slovakia</option>
                            <option value="SI">Slovenia</option>
                            <option value="SB">Solomon Islands</option>
                            <option value="SO">Somalia</option>
                            <option value="ZA">South Africa</option>
                            <option value="GS">South Georgia and South Sandwich Islands</option>
                            <option value="KR">South Korea</option>
                            <option value="ES">Spain</option>
                            <option value="LK">Sri Lanka</option>
                            <option value="SD">Sudan</option>
                            <option value="SR">Suriname</option>
                            <option value="SJ">Svalbard and Jan Mayen Islands</option>
                            <option value="SZ">Swaziland</option>
                            <option value="SE">Sweden</option>
                            <option value="CH">Switzerland</option>
                            <option value="SY">Syria</option>
                            <option value="TW">Taiwan</option>
                            <option value="TJ">Tajikistan</option>
                            <option value="TZ">Tanzania</option>
                            <option value="TH">Thailand</option>
                            <option value="TL">Timor-Leste</option>
                            <option value="TG">Togo</option>
                            <option value="TK">Tokelau</option>
                            <option value="TO">Tonga</option>
                            <option value="TT">Trinidad and Tobago</option>
                            <option value="TN">Tunisia</option>
                            <option value="TR">Turkey</option>
                            <option value="TM">Turkmenistan</option>
                            <option value="TC">Turks and Caicos Islands</option>
                            <option value="TV">Tuvalu</option>
                            <option value="UG">Uganda</option>
                            <option value="UA">Ukraine</option>
                            <option value="AE">United Arab Emirates</option>
                            <option value="GB">United Kingdom</option>
                            <option value="UM">United States Minor Outlying Islands</option>
                            <option value="UY">Uruguay</option>
                            <option value="UZ">Uzbekistan</option>
                            <option value="VU">Vanuatu</option>
                            <option value="VA">Vatican City</option>
                            <option value="VE">Venezuela</option>
                            <option value="VN">Vietnam</option>
                            <option value="VG">Virgin Islands ( British )</option>
                            <option value="VI">Virgin Islands ( U.S. )</option>
                            <option value="WF">Wallis and Futuna Islands</option>
                            <option value="EH">Western Sahara</option>
                            <option value="YE">Yemen</option>
                            <option value="ZM">Zambia</option>
                            <option value="ZW">Zimbabwe</option>
                        </select>';
    }

    /**
     * Form city field.
     *
     * @access public
     * @return void
     */
    public function form_city_field() {
        echo sprintf( '<label for="pcl_city" class="required">%s</label>', esc_html__( 'City', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[city]" id="pcl_city" class="" type="text" required/>' );
    }

    /**
     * Form sate field.
     *
     * @access public
     * @return void
     */
    public function form_state_code_field() {
        echo sprintf( '<label for="pcl_state_code" class="required">%s</label>', esc_html__( 'State/Province', 'pcl' ) );
        echo sprintf( '<select id="state_code" name="pcl_registration[state_code]" required><option value="AB">AB-Alberta</option><option value="BC">BC-British Columbia</option><option value="MB">MB-Manitoba</option><option value="NB">NB-New Brunswick</option><option value="NL">NL-Newfoundland and Labrador</option><option value="NT">NT-Northwest Territories</option><option value="NS">NS-Nova Scotia</option><option value="NU">NU-Nunavut</option><option value="ON">ON-Ontario</option><option value="PE">PE-Prince Edward Island</option><option value="QC">QC-Quebec</option><option value="SK">SK-Saskatchewan</option><option value="YT">YT-Yukon</option></select>' );
    }

    /**
     * Form zip field.
     *
     * @access public
     * @return void
     */
    public function form_zip_field() {
        echo sprintf( '<label for="pcl_zip" class="required">%s</label>', esc_html__( 'Postal Code', 'pcl' ) );
        echo sprintf( '<input name="pcl_registration[zip]" id="pcl_zip" class="" type="text" required/>' );
    }

    /**
     * Form recaptcha field.
     *
     * @access public
     * @return void
     */
    public function form_recaptcha_field() {
        do_action( 'pcl_registraion_before_recaptcha' );

        if ( get_option( 'pcl-enable-recaptcha', false ) ) :
            echo sprintf( '<div class="g-recaptcha" data-sitekey="%s"></div>', esc_attr( get_option( 'pcl-recaptcha-site-key', '' ) ) );
         endif;
    }

    /**
     * Form register button.
     *
     * @access public
     * @return void
     */
    public function form_register_button() {
        echo sprintf( '<input type="hidden" name="custom_register_nonce" value="%s" />', esc_html( wp_create_nonce( 'custom-register-nonce' ) ) );
        wp_nonce_field( 'pcl-register', 'pcl_registration_form' );
        echo sprintf( '<input type="submit" value="%s" />', esc_html__( 'Register', 'pcl' ) );
    }

    /**
     * Form redirect.
     *
     * @access public
     * @return void
     */
    public function register_form_redirect() {
        $slug = pcl_page_slug( 'register' );

        if ( $slug ) :
            wp_safe_redirect( home_url( $slug ) );
            exit;
        endif;
    }

    /**
     * Add new user.
     *
     * @access public
     * @return void
     */
    public function add_new_user() {
        if ( ! isset( $_POST['pcl_registration_form'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pcl_registration_form'], 'pcl-register' ) ) ) {
            return;
        }

        if ( isset( $_POST['pcl_registration'] ) ) {
            $fields = array_map( 'sanitize_text_field', wp_unslash( $_POST['pcl_registration'] ) );
        }

        $check_fields = array(
            'firstname',
            'lastname',
            'country',
            'city',
            'zip',
            'company',
            'phone',
        );

        // check username - required.
        $this->check_username( $fields['username'] );

        // check email - required.
        $this->check_email( $fields['email'] );

        // check password - required.
        $this->check_password( $fields['password'], $fields['password_check'] );

        // loop through our fields to check, only check if they exist.
        foreach ( $check_fields as $check_field ) :
            if ( isset( $fields[ $check_field ] ) ) :
                $func = "check_{$check_field}";
                $this->$func( $fields[ $check_field ] );
            endif;
        endforeach;

        // check title - function does not exist.
        // $this->check_title($fields['title']);
        // check state_code - function does not exist.
        // $this->check_state_code($fields['state_code']);
        // check recaptcha, if active.
        if ( get_option( 'pcl-enable-recaptcha', false ) ) {
            $recapcha = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';
            $this->check_recaptcha( $recapcha );
        }

        // only create the user in if there are no errors.
        if ( ! pcl_has_error_messages() ) {
            $this->add_user( $fields, $_POST );
        }
    }

    /**
     * Check username.
     *
     * @access protected
     * @param string $username (default: '').
     * @return void
     */
    protected function check_username( $username = '' ) {
        // Username already registered.
        if ( username_exists( $username ) ) {
            pcl_add_error_message( 'username_unavailable', 'Username already taken' );
        }

        // invalid username.
        if ( ! validate_username( $username ) ) {
            pcl_add_error_message( 'username_invalid', 'Invalid username' );
        }

        // empty username.
        if ( '' == $username ) {
            pcl_add_error_message( 'username_empty', 'Please enter a username' );
        }
    }

    /**
     * Check email.
     *
     * @access protected
     * @param string $email (default: '').
     * @return void
     */
    protected function check_email( $email = '' ) {
        // invalid email.
        if ( ! is_email( $email ) ) {
            pcl_add_error_message( 'email_invalid', 'Invalid email' );
        }

        // Email address already registered.
        if ( email_exists( $email ) ) {
            pcl_add_error_message( 'email_used', 'Email already registered' );
        }
    }

    /**
     * Check password.
     *
     * @access protected
     * @param string $password (default: '').
     * @param string $password_check (default: '').
     * @return void
     */
    protected function check_password( $password = '', $password_check = '' ) {
        // passwords empty.
        if ( '' == $password || '' == $password_check ) {
            pcl_add_error_message( 'password_empty', 'Please enter a password' );
        }

        // passwords do not match.
        if ( $password != $password_check ) {
            pcl_add_error_message( 'password_mismatch', 'Passwords do not match' );
        }
    }

    /**
     * Cechk recaptcha.
     *
     * @access protected
     * @param string $recaptcha_response (default: '').
     * @return void
     */
    protected function check_recaptcha( $recaptcha_response = '' ) {
        $secret = get_option( 'pcl-recaptcha-secret-key', '' ); // secret key.
        $response = null; // empty response.
        $recaptcha = new ReCaptcha( $secret ); // check secret key.

        if ( isset( $recaptcha_response ) ) {
            $response = $recaptcha->verifyResponse(
                isset( $_SERVER['REMOTE_ADDR'] ) ? wp_unslash( $_SERVER['REMOTE_ADDR'] ) : '',
                $recaptcha_response
            );
        }

        if ( null == $response || ! $response->success ) {
            pcl_add_error_message( 'recaptcha', 'Issue with the recaptcha' );
        }
    }


    /**
     * Check first name.
     *
     * @access protected
     * @param string $firstname (default: '').
     * @return void
     */
    protected function check_firstname( $firstname = '' ) {
        // first empty.
        if ( '' == $firstname ) {
            pcl_add_error_message( 'firstname_empty', 'Please enter a first name.' );
        }
    }

    /**
     * Check last name.
     *
     * @access protected
     * @param string $lastname (default: '').
     * @return void
     */
    protected function check_lastname( $lastname = '' ) {
        // lastname empty.
        if ( '' == $lastname ) {
            pcl_add_error_message( 'lastname_empty', 'Please enter a last name.' );
        }
    }

    /**
     * Check company.
     *
     * @access protected
     * @param string $company (default: '').
     * @return void
     */
    protected function check_company( $company = '' ) {
        // company empty.
        if ( '' == $company ) {
            pcl_add_error_message( 'company_empty', 'Please enter a company name.' );
        }
    }

    /**
     * Check phone.
     *
     * @access protected
     * @param string $phone (default: '').
     * @return void
     */
    protected function check_phone( $phone = '' ) {
        // phone empty.
        if ( '' == $phone ) {
            pcl_add_error_message( 'phone_empty', 'Please enter sa phone number.' );
        }
    }

    /**
     * Check city.
     *
     * @access protected
     * @param string $city (default: '').
     * @return void
     */
    protected function check_city( $city = '' ) {
        // city empty.
        if ( '' == $city ) {
            pcl_add_error_message( 'city_empty', 'Please enter a city.' );
        }
    }

    /**
     * Check zip..
     *
     * @access protected
     * @param string $zip (default: '').
     * @return void
     */
    protected function check_zip( $zip = '' ) {
        // zip empty.
        if ( '' == $zip ) {
            pcl_add_error_message( 'zip_empty', 'Please enter a postal code.' );
        }
    }

    /**
     * Check country.
     *
     * @access protected
     * @param string $country (default: '').
     * @return void
     */
    protected function check_country( $country = '' ) {
        // country empty.
        if ( '' == $country ) {
            pcl_add_error_message( 'country_empty', 'Please select a country.' );
        }
    }

    /**
     * Add user.
     *
     * @access protected
     * @param array $fields (default: array()).
     * @param array $post_data (default: array()).
     * @return void
     */
    protected function add_user( $fields = array(), $post_data = array() ) {
        $user_login = $fields['username'];
        $user_pass = $fields['password'];
        $redirect = get_option( 'pcl-register-redirect', home_url() );

        if ( ! isset( $fields['firstname'] ) ) :
            $first_name = '';
        else :
            $first_name = $fields['firstname'];
        endif;

        if ( ! isset( $fields['lastname'] ) ) :
            $last_name = '';
        else :
            $last_name = $fields['lastname'];
        endif;

        do_action( 'pcl_before_user_registration', $fields, $post_data );

        $user_args = array(
            'user_login' => $user_login,
            'user_pass' => $user_pass,
            'user_email' => $fields['email'],
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_registered' => date( 'Y-m-d H:i:s' ),
            'role' => 'subscriber',
        );
        $user_args = apply_filters( 'pcl_insert_user_args', $user_args, $fields, $post_data );

        $new_user_id = wp_insert_user( $user_args );

        do_action( 'pcl_after_user_registration', $new_user_id, $fields, $post_data );

        if ( $new_user_id ) :
            // send an email to the admin alerting them of the registration.
            pickle_custom_login()->email->send_email( array( 'user_id' => $new_user_id ) );

            // check our activation flags - admin activation, user (email) activation, other.
            if ( pcl_require_admin_activation() ) :
                $this->admin_activate_account_required = true;
            elseif ( pcl_is_activation_required() ) :
                $this->activate_account_required = true;
            else :
                // log the new user in
                wp_set_auth_cookie( $new_user_id );
                wp_set_current_user( $new_user_id, $user_login );
                do_action( 'wp_login', $user_login );

                // send the newly created user to the redirect page after logging them in.
                wp_safe_redirect( $redirect );
                exit;
            endif;
        endif;
    }

}
