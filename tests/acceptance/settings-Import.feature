Feature: SSP Plugin Import Settings
	In order to setup the SSP plugin
	As an admin user
	I need to be able to change import plugin settings

	Background:
		Given I login as admin
		When I click "Podcast" submenu "Settings"
		Then I can see that "General" tab is active
		When I click tab "Import"
		Then I can see that "Import" tab is active

	Scenario: All hosting settings exist
		Then I can see "Use this option for a one time import of your existing WordPress podcast to your Castos account."
		And I can see "If you have a podcast hosted on an external service"
		And I can see "RSS feed"
		And I can see "Post Type"
		And I can see "Begin import Now"
