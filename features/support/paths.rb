def path_to(page_name)
  path = case page_name
  
  when /the homepage/i
    root_path
  
  # Add more page name => path mappings here
  
  else
    page_name
  end
  
  "http://localhost/cdm_rest#{path}"
end