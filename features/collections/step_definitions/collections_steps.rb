require 'json'

Then /^I should get a list of collections in JSON format$/ do
  @collections = JSON.parse(webrat.response.body)
  @collections.should be_a_kind_of(Array)
end

Then /^I should see the full list of collections$/ do
  @collections.should be_a_kind_of(Array)
  @collections[0].should include(
    "alias" => "/p4006coll2",
    "name" => "Governors of Michigan",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll2"
  )
  @collections[1].should include(
    "alias" => "/p4006coll3",
    "name" => "Civil War Photographs",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll3"
  )
  @collections[2].should include(
    "alias" => "/p4006coll7",
    "name" => "Lighthouses and Life-Saving Stations",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll7"
  )
  @collections[3].should include(
    "alias" => "/p4006coll4",
    "name" => "Early Photography",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll4"
  )
  @collections[4].should include(
    "alias" => "/p4006coll5",
    "name" => "Sheet Music",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll5"
  )
  @collections[5].should include(
    "alias" => "/p4006coll8",
    "name" => "Main Streets",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll8"
  )
  @collections[6].should include(
    "alias" => "/p4006coll10",
    "name" => "Architecture",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll10"
  )
  @collections[7].should include(
    "alias" => "/p4006coll15",
    "name" => "Civil War Service Records",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll15"
  )
  @collections[8].should include(
    "alias" => "/p4006coll17",
    "name" => "Oral Histories",
    "path" => "D:\\Sites\\129401\\Data\\p4006coll17"
  )
  @collections[9].should include(
    "alias" => "/p129401coll0",
    "name" => "WPA Property Inventories",
    "path" => "D:\\Sites\\129401\\data\\p129401coll0"
  )
  @collections[10].should include(
    "alias" => "/p129401coll3",
    "name" => "Maps",
    "path" => "D:\\Sites\\129401\\data\\p129401coll3"
  )
  @collections[11].should include(
    "alias" => "/p129401coll7",
    "name" => "Death Records, 1897-1920",
    "path" => "D:\\Sites\\129401\\data\\p129401coll7_1"
  )
  @collections[12].should include(
    "alias" => "/p129401coll10",
    "name" => "Michigan Polish Americans",
    "path" => "D:\\Sites\\129401\\data\\p129401coll10"
  )
end