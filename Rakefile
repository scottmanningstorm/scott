desc "Quickly dump database MYSQL file onto base directory"
task :mysqldump do
  STDOUT.puts "Which database?"
  db = STDIN.gets.strip
  system("/Applications/MAMP/Library/bin/mysqldump -u root -p --opt #{db} > tests/_data/dump.sql")
end

desc "Restore database from a dump file"
task :mysqlrestore do
  STDOUT.puts "Name the file of the SQL dump"
  db = STDIN.gets.strip
  system("/Applications/MAMP/Library/bin/mysql -u root -p #{db} < #{db}.sql")
end

desc "Start the PHP Server"
task :server do 
    ip = `ifconfig en0`.match(/192\.168\.\d{1,3}\.\d{1,3}/).to_s
    ip = ip + ":9292"
    puts(ip)
    system("php -S "+ip)
end

desc "Run unit tests"
task :units do
    puts "Running all Unit tests through Codeception"
    run_tests()
end

desc "Create a new unit test"
task :new_unit do
  name = ARGV.last
  system("php codecept.phar generate:test unit #{name}")
  task name.to_sym do ; end
end

desc "Run a specified unit param unit test name"
task :rununit do
  name = ARGV.last
  system("php codecept.phar run tests/unit/#{name}.php ")
  task name.to_sym do ; end
end

task :dbstart do
  system("php cmd/execute_dump_start.php")
end

task :dbexecute do
  system("php cmd/execute_dump.php")
end

task :acceptance do
    puts "Running all Accpetance tests through Codeception"
    run_tests("acceptance")
end

desc "Deploys the website with only the styles ammended"
task :quickdeploy do
  puts "--> Running grunt commands"
  output = system("php cmd/deploy-settings.php")
  if output
    system("php cmd/styles_iterator.php")
    system("compass watch")
    system("grunt sass")
    system("grunt cssmin")
    system("grunt uglify")
    system('ant upload_files')
    output = system("php cmd/deploy-settings.php back")

    if output
      system('grunt sass')
      growl_notify("Deployment complete!", "")
    end
  end
end

desc "Deploys the website after running tests and clean ups"
task :deploy do
  puts "--> Running all system tests before deployment..."
  result = run_tests("all")
  if result
    system("php cmd/styles_iterator.php")
    system("compass watch")
    puts "--> All Tests successfully passed"
    puts "--> Initiating PHP clean up..."
    system("php-cs-fixer fix . -fixers=linefeed,short_tag,indentation,trailing_spaces,unused_use,phpdoc_params,visibility,return,braces,extra_empty_lines,elseif,php_closing_tag")
    puts "--> Running grunt commands"
    system("php cmd/deploy-settings.php up")
    system("grunt")
    puts "--> PHP all cleaned up - pushing up to GitHub"
    lazy_git()
    puts "--> Uploading files to production server"
    system('ant upload_files')
    puts "--> Deployment completed."
    system("php cmd/deploy-settings.php back")
    growl_notify("Deployment complete and successful!", "")
  end
end

desc "Create a style guide"
task :styleguide do
  system('compass compile && ./node_modules/.bin/styledocco -n "Site Styleguide" --preprocessor "sass --compass" assets/styles/sass/')
end

desc "A lazy push task"
task :push do
  lazy_git('master')
end

task :selenium do
  system('java -jar tests/selenium-server-standalone-2.35.0.jar')
end

def lazy_git(branch="development")
  system('git add .')
  system('git add -A')
  system("git commit -m 'Deloyment - updating with master branch'")
  system("git push origin #{branch}")
end

def run_tests(type="unit")
    if type == 'all'
        type = ""
    end
    result = system("php codecept.phar run #{type}")
    type = type.capitalize
    if result
        message = "#{type} Tests Passed!"
        image = ""
    else
        message = "#{type} Tests Failed!"
        image = ""
    end
    growl_notify(message, image)
    return result
end

def growl_notify(message, image="") 
    if !image.nil?
        image = "--image '#{image}'"
    end
    system("growlnotify #{image} -m '#{message}'")
end

#Usage
#rake model[test]
desc "Create a Model"
task :model, :arg1 do |t, args|
  model_name = args[:arg1].capitalize
  puts "Actioning the basic model GUI to build #{model_name} model"
  puts "Going to build Unit Test for #{model_name}"
  system("php codecept.phar generate:cept unit Test#{model_name}Cept")
  system("php cmd/b_mod.php")
end

desc "Cleans up all PHP Code"
task :phpcleanup do
  system("php-cs-fixer fix .")
end

#Usage
#rake controller
desc "Create just a simple controller"
task :controller do
  STDOUT.puts "What would you like this controller to be called?"
  input = STDIN.gets.strip
  methods = ""

  controller_name = input.capitalize
  puts "Building the Controller Test ( Cest in other words ) for #{controller_name}"
  system("php codecept.phar generate:cept unit Test#{controller_name}Cest")

  view_directory = "app/views/templates/#{input}"
  controller_path = "app/controllers/#{input}.php"

  puts "Creating View Directory: #{view_directory}"
  FileUtils.mkdir_p(view_directory) unless File.exists?(view_directory)
  
  if( !input.nil? )
    method = ""
    while method != 'n'
      STDOUT.puts "Please name a method to add to the controller...type n to stop"
      method = STDIN.gets.strip
      if method != "n" && !method.nil?
        methods << "public function #{method}() {} \n\n"
        puts "Creating view files for #{method} within #{input} views directory"

        view_file = ""
        view_file = view_directory+"/#{method}.php"
        
        File.open(view_file, "w") {|file| file.puts "<h1>#{method}</h1>"}
      
        puts "Building acceptance test for #{method}"
        system("php codecept.phar generate:cept acceptance Test#{method.capitalize}Cept")

        
      end
    end
  end

  contents = "<?php
    class #{input.capitalize} extends \\core\\site\\C_Controller {

        public function index()
        {

        }

        #{methods}

    }
  ?>"

  puts "Creating controller #{controller_path}"
  File.open(controller_path, "w") {|file| file.puts contents}

  puts "Action of Controller has now all been done. Thank you, and come again!"
end

desc "Create a javascript file: choice of backbone or require"
task :javascript do
  STDOUT.puts "Standard Require script (R) or Backbone View (B)?"
  script_type = STDIN.gets.strip

  if !script_type.nil?
    STDOUT.puts "Enter the script name."
    script_name = STDIN.gets.strip

    dependencies = []
    dependency = ""

    while dependency != "n"
      STDOUT.puts "Enter dependency title:"
      dependency = STDIN.gets.strip  
      if dependency != 'n'
        dependencies.push("'#{dependency}'")
      end
    end

    if script_type == 'b' || script_type == 'B'
      example_file = "assets/scripts/views/backbone-example.js"
      create_file = "assets/scripts/views/#{script_name}.js"
      message = "Backbone view File successfully created"
      dependencies = (dependencies.length > 0 ? "," + dependencies.join(",") : "")
    else
      example_file = "assets/scripts/app/_require-example.js"
      create_file = "assets/scripts/app/#{script_name}.js"
      message = "Require JS File successfully created"
      dependencies = (dependencies.length > 0 ? dependencies.join(",") : "")
    end

    example_file = File.read(example_file)

    replace = example_file.gsub('<<dependencies>>', dependencies)
    File.open(create_file, "w") {|file| file.puts replace}
    
    puts message
  end
end

task :default => ["tests"]

desc "Create a listing page and all the necesary files"
task :build do
  puts "Create a listing page"
  system("php cmd/build/runner.php")
end

#Example
#rake mock[test,10]
desc "Mock some data"
task :mock, :table, :number do | t, item |
  puts "Mocking some data"
  system("php cmd/mock_data.php #{item[:table]} #{item[:number]}")
end

#Usage
#rake sass_bulk
desc "Recompile all SCSS files if a merge results in conflicts"
task :sass_bulk do

  puts "Recompiling all SCSS files"
  puts "Note: SASS NEEDS TO BE RUNNING"

  files = Dir[ "assets/styles/sass/*.scss" ]

  for file in files do
      contents = File.read(file)

      fo = File.open(file,'w')
      fo.write(contents);
  end

  puts "Bulk complile complete!"
end

#New git add variation
##Add all files like normal but dumps the database into some file ready for the migration
desc "Dump SQL for a better migration and then add all file like normal"
task :add do
  
  system("php cmd/dump_mysql.php")
  system("git add . -A")
end

#Creates a blog template that just needs to be styled
desc "Adding a blog to the current project"
task :blog do
    puts("Adding a blog to the current project")
    system("php cmd/blog/blog.php")
end

desc "Opens the current project in the default browser"
task :view do
  path = Dir.pwd.split('/')
  project = path.last

  system("open http://localhost:8888/#{project}")
end

desc "Adds our routes to a cache file"
task :cache_routes do
    puts("\n Adding routes to a cache file.... \n ")
    system("php cmd/CacheRoute.php add")
end 

desc "Deletes our cached routes file if we have one"
task :delete_cached_routes do
    puts("\n Deleting cached routes.... \n ")
    system("php cmd/CacheRoute.php delete")
end 

def create_repo(file_name) 
  if (!file_name.match('Repository'))
      file_name = file_name+'Repository'
  end
  system("php cmd/build/RepositoryBuilder.php #{file_name}")
end

def create_model(file_name)
  system("php cmd/build/ModelBuilder.php #{file_name}")
end

def create_controller(file_name, type='') 
  system("php cmd/build/ControllerBuilder.php #{file_name} #{type}")
end

def create_controller(file_name) 
  system("php cmd/build/buildRController.php #{file_name}")
end

def create_view(folder_name, type)
  if (type == 'admin')
    path = 'app/views/admin/templates/'
  else 
    path = 'app/views/templates/'
  end
  puts("\nView file will be placed in #{path}#{folder_name}/ \nPlease name the view file...") 
  file_name = STDIN.gets.strip
  system("php cmd/build/ViewBuilder.php #{folder_name} #{file_name} #{path}")
 
end

desc "Builds a Model"
task :build_model  do 
  file_name = ARGV[1]
  task file_name.to_s do ; end 
  create_model(file_name)
end

desc "Builds a Repository"
task :build_repo do 
  file_name = ARGV[1]
  task file_name.to_s do ; end
  if (!File.exist?(file_name))
    puts("\n There is no model associated with the repository your about to create. Would you like to create the Model [Y], [N]\n")
    question = STDIN.gets.strip.capitalize

    if (question === 'Y')
      create_model(file_name)
    end
  end
  create_repo(file_name) 
end

desc "Builds a View"
task :build_view do 
  file_name = ARGV[1]
  task file_name.to_s do ; end 
  create_view(file_name, 'public')
end

desc "Builds admin View"
task :build_admin_view do 
  file_name = ARGV[1]
  task file_name.to_s do ; end 
  create_view(file_name, 'admin')
end

desc "Builds a Controller"
task :build_controller do 
  file_name = ARGV[1]
  task file_name.to_s do ; end 
  create_controller(file_name, false)
end

desc "Builds a Controller"
task :build_resourceful_controller do 
  file_name = ARGV[0]
  task file_name.to_s do ; end 
  create_resourceful_controller(file_name)
end

desc "Adds a new Model. Switches avable: v, c, r. Creates new Model, View or Repository"
task :mvc do

  file_name = ARGV[1]
  task file_name.to_s do ; end 
  task ARGV[2] do ; end
  task ARGV[3] do ; end
  task ARGV[4] do ; end 
  task ARGV[5] do ; end 
  puts "\n" 

  if (file_name == 'h' || file_name == 'r' || file_name == 'v' || file_name == 'c' || file_name == nil)
    puts("--help Usage: rake mvc [file_name] [SWITCHES] \n")
    puts("")
    puts("'m' Builds a model.php file \n") 
    puts("'r' Builds  a Repository.php file \n") #Build and check if we have model if no ask to build.
    puts("'v' Builds a public View.php file \n") #Check 
    puts("'V' Builds an admin View.php file \n") #Check 
    puts("'c' Builds a Controller.php file \n") #Just build controller.
    puts("'C' Builds an Admin Controller.php file -Extends admin controller \n")
    puts("'rc' Builds a Resourceful Controller.php file -Resourceful Controller is built with predefined Add, Edit, Create and Update functions. \n")
    exit
  end

  #create model
  if (ARGV.include? "m")
    create_model(file_name)
  end
  #create repository
  if (ARGV.include? "r")
    create_repo(file_name) 
  end
  #create public view
  if (ARGV.include? "v") 
    create_view(file_name, 'public')
  end
  #create admin view
  if (ARGV.include? "V") 
    create_view(file_name, 'admin')
  end
  #create controller
  if (ARGV.include? "c")
    create_controller(file_name)
  end
  #create admin controller
  if (ARGV.include? "C")
    create_controller(file_name, 'admin')
  end
  #create resource controller
  if (ARGV.include? "rc")
    create_controller(file_name)
  end

end

desc "Quick lookup of PHP functions from the php.net manual"
task :php do 
  search = ARGV[1]
  task search.to_s do ; end 
  system("open http://php.net/manual-lookup.php?pattern=#{search}")
end

desc "Quick fire up of phpmyadmin"
task :myadmin do 
  #Local host or rake server? 
  system("open http://localhost:8888/MAMP/index.php?page=phpmyadmin&language=English")
end

desc "Creates a new library php file"
task :create_library do 
  file_name = ARGV[1]
  task file_name.to_s do; end
  out_file = File.new("app/libraries/out.php", "w")
  out_file.puts("write your stuff here") #call php - build empty class. 
  out_file.close
end

desc "Opens up selected Git - If no param passed, Storms git will be loaded."
task :git do  

  argv = ARGV.last
  task argv.to_s do; end 
  argv =  argv.downcase
  
  git_profile = 'StormCreative'
  
  if (argv == 'ash' || argv == 'b')
    git_profile = 'banksy89'
  end
  if (argv == 'scott' || argv == 's')
    git_profile = 'scottmanningstorm'
  end
  if (argv == 'alex' || argv == 'a')
    git_profile = 'AlexShearcroft'
  end
  if (argv == 'nathan' || argv == 'n')
    git_profile = 'nathandodds'
  end

  system("open http://github.com/"+git_profile)

end

