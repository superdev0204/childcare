require 'compass/import-once/activate'

project_path = "./"
sass_dir = "./"
css_dir = "../"

relative_assets = true

if environment == :production
	line_comments = false
    output_style = :compressed
    sass_options = {:debug_info => false}	
	
	require 'fileutils'
        on_stylesheet_saved do |file|
            if File.exists?(file)
            filename = File.basename(file, File.extname(file))
            File.rename(file, css_dir + "/" + filename + ".min" + File.extname(file))
        end
        on_stylesheet_saved do
          `compass compile -c config.rb -e development --force`
        end

    end    
else
	line_comments = true
end


