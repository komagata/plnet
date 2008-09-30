namespace :gettext do
  desc "Update pot/po files."
  task :updatepo do
    require 'gettext/utils'
    GetText.update_pofiles(
      "plnet",
      Dir.glob("{app,config,components,lib}/**/*.{rb,rhtml,rxml,html.erb,erb.builder}"),
      "plnet0.0.1"
    )
  end

  desc "Create mo-files"
  task :createmo do
    require 'gettext/utils'
    GetText.create_mofiles(true, "po", "locale")
  end
end
