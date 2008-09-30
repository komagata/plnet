namespace :db do
  desc "Migrate the database through scripts in db/migrate. Target specific version with VERSION=x. Turn off output with VERBOSE=false."
  task :migrate => :environment do
    ActiveRecord::Migration.verbose = ENV["VERBOSE"] ? ENV["VERBOSE"] == "true" : true
    ActiveRecord::Migrator.migrate("db/migrate/", ENV["VERSION"] ? ENV["VERSION"].to_i : nil)
    Rake::Task["db:schema:dump"].invoke if ActiveRecord::Base.schema_format == :ruby
    Rake::Task["annotate_models"].invoke
  end

  namespace :fixtures do
    desc "Save fixtures from the current environment's database"
    task :save => :environment do
      Dir["app/models/*.rb"].each{|i| eval File.basename(i, '.rb').classify}
      Object.subclasses_of(ActiveRecord::Base).each{|klass| klass.to_fixture rescue p klass.name}
    end
  end
end

