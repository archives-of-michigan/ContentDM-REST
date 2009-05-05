Feature: root
  In order to provide a top-level list of resources
  As a web service consumer
  I want to see a list of available resources

  Scenario: Listing
    When I go to /
    Then I should see a link to "/collections"