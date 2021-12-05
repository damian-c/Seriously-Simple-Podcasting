<?php

use Codeception\Step\Argument\PasswordArgument;
use Codeception\Util\Locator;
use function PHPUnit\Framework\assertTrue;


/**
 * Acceptance tester functions.
 *
 * Inherited Methods
 * @method void wantToTest( $text )
 * @method void wantTo( $text )
 * @method void execute( $callable )
 * @method void expectTo( $prediction )
 * @method void expect( $prediction )
 * @method void amGoingTo( $argumentation )
 * @method void am( $role )
 * @method void lookForwardTo( $achieveValue )
 * @method void comment( $description )
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {

	use _generated\AcceptanceTesterActions;

	protected $browser;


	const DEFAULT_EPISODE_FILE = 'https://episodes.castos.com/podcasthacker/d21a1b7a-531f-48f1-b4c0-8b8add2bccfe-file-example.mp3';


	public function __construct( \Codeception\Scenario $scenario ) {


		parent::__construct( $scenario );
	}

	public function _inject( \Codeception\Lib\InnerBrowser $browser ) {
		$this->browser = $browser;
	}


	/**
	 * Because we test on remote server, standard functions like loginAsAdmin can not be used,
	 * so lets rewrite them with user steps.
	 */
	public function loginAsAdmin() {
		$this->amOnPage( '/wp-login.php' );
		$this->see( 'Username or Email Address' );
		$this->fillField( '#user_login', new PasswordArgument( $_ENV['SITE_USER'] ) );
		$this->fillField( '#user_pass', new PasswordArgument( $_ENV['SITE_USER_PASS'] ) );
		$this->click( '#wp-submit' );

		// Fix PhpBrowser bug when it doesn't update the current url and keeps it as /login.php
		$this->amOnPage( '/wp-admin/' );
	}

	public function amOnPluginsPage() {
		$this->amOnPage( '/wp-admin/plugins.php' );
	}


	// Gherkin functions.

	/**
	 * @Given I am on the plugins page
	 * @Given I go to the plugins page
	 */
	public function iAmOnThePluginsPage() {
		$this->amOnPluginsPage();
	}

	/**
	 * @When I am on the homepage
	 */
	public function iAmOnTheHomepage() {
		$this->amOnPage( '/' );
	}

	/**
	 * @When I login as admin
	 */
	public function iLoginAsAdmin() {
		$this->loginAsAdmin();
	}

	/**
	 * @Then I can see :arg1
	 * @Then I can see :arg1 text
	 */
	public function iCanSee( $arg1 ) {
		$this->see( $arg1 );
	}

	/**
	 * @Given I can see SSP plugin is deactivated
	 */
	public function iCanSeeSspIsDeactivated() {
		$this->see( 'Activate', '#activate-seriously-simple-podcasting' );
	}

	/**
	 * @When I activate the SSP plugin
	 */
	public function iActivateTheSSPPlugin() {
		$this->click( '#activate-seriously-simple-podcasting' );
		$this->wait( 2 );
	}

	/**
	 * @When I activate the Classic Editor plugin
	 */
	public function iActivateTheClassicEditorPlugin() {
		$this->click( '#activate-seriously-simple-podcasting' );
		$this->wait( 2 );
	}

	/**
	 * @Then I can see SSP plugin is activated
	 */
	public function iCanSeeSSPPIsActivated() {
		$this->see( 'Deactivate', '#deactivate-seriously-simple-podcasting' );
	}

	/**
	 * @When I deactivate the SSP plugin
	 */
	public function iDeactivateTheSSPPlugin() {
		$this->click( '#deactivate-seriously-simple-podcasting' );
		$this->wait( 2 );
	}

	/**
	 * @Then I can see the Onboarding Wizard
	 */
	public function iCanSeeTheOnboardingWizard() {
		$this->see( "Let's get your podcast started" );
	}

	/**
	 * @Then I can not see the Onboarding Wizard
	 */
	public function iCanNotSeeTheOnboardingWizard() {
		$this->dontSee( "Let's get your podcast started" );
	}

	/**
	 * @Then I can see that I am on the :arg1 step of onboarding wizard
	 */
	public function iCanSeeThatIAmOnTheStep( $arg1 ) {
		$this->see( $arg1, '.ssp-onboarding__step.active' );
	}

	/**
	 * @When I click :arg1 link
	 */
	public function iClickLink( $arg1 ) {
		$this->click( $arg1, 'a' );
	}

	/**
	 * @When I fill the :arg1 with :arg2
	 */
	public function iFillTheFieldWith( $arg1, $arg2 ) {
		$map = $this->getFieldsMap();

		assertTrue( array_key_exists( $arg1, $map ) );

		$this->fillField( $map[ $arg1 ], $arg2 );
	}


	/**
	 * @When I click :arg1 button
	 */
	public function iClickButton( $arg1 ) {
		$this->click( $arg1, 'button' );
	}

	/**
	 * @When I save settings
	 */
	public function iSaveSettings() {
		$this->click( '#ssp-settings-submit' );
	}

	/**
	 * @When I select the :arg1 as :arg2
	 */
	public function iSelectTheFieldOption( $arg1, $arg2 ) {
		$map = $this->getFieldsMap();

		assertTrue( array_key_exists( $arg1, $map ) );

		$this->selectOption( $map[ $arg1 ], $arg2 );
	}

	public function getFieldsMap() {
		return array(
			'Show name'                         => '#show_name',
			'Show description'                  => '#show_description',
			'Primary Category'                  => '#data_category',
			'Primary Sub-Category'              => '#data_subcategory',
			'Feed details Title'                => '#data_title',
			'Feed details Description/Summary'  => '#data_description',
			'Feed details Primary Category'     => '#data_category',
			'Feed details Primary Sub-Category' => '#data_subcategory',
			'Podcast post types Posts'          => '#use_post_types_post',
			'Posts menu'                        => '#menu-posts ul.wp-submenu > li',
			'Episode title'                     => '#title',
			'Episode content'                   => '#content',
			'Episode file'                      => '#upload_audio_file',
			'File size'                         => '#filesize',
			'Date recorded'                     => '#date_recorded_display',
		);
	}

	/**
	 * @Then I can see field :arg1 contains :arg2
	 */
	public function iCanSeeFieldArgContains( $arg1, $arg2 ) {
		$map = $this->getFieldsMap();
		assertTrue( array_key_exists( $arg1, $map ) );

		$this->seeInField( $map[ $arg1 ], $arg2 );
	}

	/**
	 * @Then I can see field :arg1 contains current date in format :arg2
	 */
	public function iCanSeeFieldContainsCurrentDateInFormat( $arg1, $arg2 ) {
		$date_str = date( $arg2 );

		$this->iCanSeeFieldArgContains( $arg1, $date_str );
	}


	/**
	 * @When I go to step :arg1
	 */
	public function iGoToStepNumber( $arg1 ) {
		$this->click( $arg1, '.ssp-onboarding__step a' );
	}

	/**
	 * @Then I can see :arg1 selected as :arg2
	 */
	public function iCanSeeOptionSelectedAs( $arg1, $arg2 ) {
		$map = $this->getFieldsMap();
		assertTrue( array_key_exists( $arg2, $map ) );

		$this->seeOptionIsSelected( $map[ $arg2 ], $arg1 );
	}


	/**
	 * @When I click :arg1 submenu :arg2
	 */
	public function iClickMenuSubmenu( $arg1, $arg2 ) {
		$this->click( $arg2, sprintf( '#%s ul li a', $this->getAdminMenuId( $arg1 ) ) );
		$this->wait( 2 );
	}

	/**
	 * @When I click tab :arg1
	 */
	public function iClickTabArg( $arg1 ) {
		$this->click( $arg1, '#main-settings a.nav-tab' );
	}

	/**
	 * @Then I can see that :arg1 tab is active
	 */
	public function iCanSeeTabIsActive( $arg1 ) {
		$this->see( $arg1, '#main-settings a.nav-tab-active' );
	}

	/**
	 * @When I check :arg1 checkbox
	 */
	public function iCheckArgCheckbox( $arg1 ) {
		$map = $this->getFieldsMap();

		assertTrue( array_key_exists( $arg1, $map ) );

		$this->checkOption( $map[ $arg1 ] );
	}

	/**
	 * @Then I can see that :arg1 submenu exists in :arg2
	 */
	public function iCanSeeThatSubmenuExistsInMenu( $arg1, $arg2 ) {
		$this->see( $arg1, sprintf( '#%s ul.wp-submenu > li', $this->getAdminMenuId( $arg2 ) ) );
	}

	protected function getAdminMenuId( $menu_title ) {
		$id = 'menu-posts';
		if ( 'posts' !== $menu_title ) {
			$id .= '-' . strtolower( str_replace( ' ', '-', $menu_title ) );
		}

		return $id;
	}

	/**
	 * @When I click admin menu :arg1
	 */
	public function iClickAdminMenu( $arg1 ) {
		$this->click( $arg1, '#adminmenu > li' );
	}

	/**
	 * @Then I can see that discount widget exists
	 */
	public function iCanSeeThatDiscountWidgetExists() {
		$this->see( "Castos Hosting Discount" );
		$this->see( "Drop in your name and email and we’ll send you a coupon" );
		$this->see( "Spam sucks. We will not use your email for anything else" );
	}


	/**
	 * @Given I want to :arg1
	 */
	public function iWantTo( $arg1 ) {
		$this->wantTo( $arg1 );
	}


	/**
	 * @Then I can see link with title :arg1 and url :arg2
	 */
	public function iCanSeeExtensionLink( $arg1, $arg2 ) {

		if ( ! $this->isAbsoluteUrl( $arg2 ) ) {
			$baseUrl = $this->getConfig( 'url' );
			$arg2    = $baseUrl . $arg2;
		}

		$this->see( $arg1, Locator::href( $arg2 ) );
	}

	/**
	 * @When I save the episode
	 */
	public function iSaveTheEpisode() {
		$this->click( '#publish' );
	}

	protected function isAbsoluteUrl( $url ) {
		$pattern = "/^(?:ftp|https?|feed)?:?\/\/(?:(?:(?:[\w\.\-\+!$&'\(\)*\+,;=]|%[0-9a-f]{2})+:)*
        (?:[\w\.\-\+%!$&'\(\)*\+,;=]|%[0-9a-f]{2})+@)?(?:
        (?:[a-z0-9\-\.]|%[0-9a-f]{2})+|(?:\[(?:[0-9a-f]{0,4}:)*(?:[0-9a-f]{0,4})\]))(?::[0-9]+)?(?:[\/|\?]
        (?:[\w#!:\.\?\+\|=&@$'~*,;\/\(\)\[\]\-]|%[0-9a-f]{2})*)?$/xi";

		return (bool) preg_match( $pattern, $url );
	}
}
