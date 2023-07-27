# DISCLAIMER: This script provides an approximate count of lines of code, excluding files of Bootstrap and Visual Studio designer code etc....


import os

def has_desired_extension(file_path, extensions):
    _, ext = os.path.splitext(file_path)
    return ext.lower() in extensions


def count_lines_of_code(file_path):
    try:
        with open(file_path, 'r', encoding='utf-8') as file:
            lines = file.readlines()
            return len(lines)
    except Exception as e:
        print(f"Error reading file '{file_path}': {e}")
        return 0

def count_lines_of_code_in_directory(directory_path, exclude_dirs, extensions):
    total_lines = 0
    for root, dirs, files in os.walk(directory_path):
        
        dirs[:] = [d for d in dirs if os.path.basename(d) not in exclude_dirs]

        for file_name in files:
            file_path = os.path.join(root, file_name)

            if has_desired_extension(file_path, extensions) and not file_name.endswith("Designer.cs"):
                lines = count_lines_of_code(file_path)
                total_lines += lines
                
            # else:
            #     print(f"Excluded: {file_path}")

    return total_lines

if __name__ == "__main__":
    current_directory = os.getcwd()
    excluded_dirs = ['.themes\modern\assets', '.github', 'bootstrap', '.examples\CSharp-API-Example-none-guna']
    file_extensions = ['.js', '.cs', '.css', '.php', '.py']

    lines_of_code = count_lines_of_code_in_directory(current_directory, excluded_dirs, file_extensions)
    print(f"Total lines of code for {', '.join(file_extensions)} files: {lines_of_code}")
