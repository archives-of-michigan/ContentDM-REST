Feature: Showing a collection
  In order to obtain a single collection resource
  As a web service consumer
  I want to see a specific collection in various formats
  
  Scenario: Success
    When I request /index.php?q=/collections/p129401coll0 in JSON format
    Then it should be successful
    And I should get the desired collection in JSON format
