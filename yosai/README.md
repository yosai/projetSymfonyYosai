Custom CMS Project
==============

- [Custom CMS Project](#custom-cms-project)
    - [Base project **[10pt]**](#base-project-10pt)
        - [Project Configuration **[2pt]**](#project-configuration-2pt)
        - [Content Manager **[2pt]**](#content-manager-2pt)
        - [Hook Manager **[2pt]**](#hook-manager-2pt)
        - [Module Manager **[1pt]**](#module-manager-1pt)
        - [Route Manager **[1pt]**](#route-manager-1pt)
        - [Template Bundle **[2pt]**](#template-bundle-2pt)
    - [Bonus](#bonus)

## Base project **[10pt]**

For this project you have to find part of the files that need to be completed in order to make everything work !
You have to commit the original files to a **GIT** repository in order to let me see the improvements of your project.

### Project Configuration **[2pt]**

> The project configuration is an important part of the start of the project, please look for every configuration files and try to understand what you can. That will help you examine what are the different parts of the application. Take a look to classes that are being used and how they are used.

1. Configure the parameters.yml file with the correct values for your project
2. Add a new role in the security.yml as **ROLE\_WRITER** expending **ROLE\_USER**

### Content Manager **[2pt]**

The content manager is what manages the content of the application through helper classes.

### Hook Manager **[2pt]**

The hook manager is what manages the hooks from which modules are called and displayed.

### Module Manager **[1pt]**

The module manager is what manages module finding, installation, uninstallation and loading.

### Route Manager **[1pt]**

The route manager is what manages routes and display the correct template.

### Template Bundle **[2pt]**

The template manager is what manages templates loading and installation, and assets management.

## Bonus

> Note: Additional modules are to be placed in the **modules** folder at the root of the project.

- Adapt your own template with your modules **[4pt]**
- Make a service for module loading analysis (time, resources, ...) **[3pt]**
- Make a module to display the module loading service data **[2pt]**
- Make two more front modules for content display **[2pt]**
- Improve the *front\_content* module for search and category display **[2pt]**
- Improve the *admin\_settings\_templates* module so it can display more than one thumbnail **[1pt]**

> Do not hesitate to send me a mail if you consider this bonus list can be extended