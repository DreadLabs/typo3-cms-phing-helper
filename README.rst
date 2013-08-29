======================
typo3-cms-phing-helper
======================

.. image:: https://travis-ci.org/DreadLabs/typo3-cms-phing-helper.png?branch=master
  :target: https://travis-ci.org/DreadLabs/typo3-cms-phing-helper

A package of Phing Tasks, Filters & Types for usage with TYPO3 CMS deployment.

.. contents:: :local:

Installation
------------

Add dependency with Composer

.. code:: sh

      $ php composer require dreadlabs/typo3-cms-phing-helper

Import task definitions to your build script

.. code:: xml

      <import file="../vendor/dreadlabs/typo3-cms-phing-helper/Resources/Public/TaskDefinitions.xml" />

Components
----------

Filters
.......

LocalConfiguration
******************

Transforms the TYPO3 CMS DefaultConfiguration into a Phing property file.

Parameters

TYPO3Version (string)
   Used for replacing a unresolvable constant in the DefaultConfiguration array

Usage:

.. code:: xml

   <append destFile="./Properties/LocalConfiguration.properties">
      <filterchain>
         <filterreader classname="Filters.LocalConfigurationFilter">
            <param name="TYPO3Version" value="6.0.0" />
         </filterreader>
      </filterchain>

      <fileset dir="../www/t3lib/stddb/">
         <include name="DefaultConfiguration.php" />
      </fileset>
   </append>

Tasks
.....

GenerateLocalConfiguration
**************************

Writes a the TYPO3 CMS LocalConfiguration.php file

Parameters

file (string)
   Path of file to write the configuration into
propertyprefix (string)
   Prefix of project properties to read the LocalConfiguration keys/values from 

Usage:

.. code:: xml

   <!-- read previously generated Phing property file into project properties -->
   <property
      file="./Properties/LocalConfiguration.properties"
      prefix="LocalConfiguration" />

   <generatelocalconfiguration
      file="www/typo3conf/LocalConfiguration.php"
      propertyprefix="LocalConfiguration" />

GuessExtensionKey
*****************

Guesses the key of the extension you're currently working on

Parameters

strip (int)
   Amount of parts to strip from path
property (string)
   Name of property to write guessed extension key into

Usage:

.. code:: xml

   <guessextensionkey strip="1" property="extension.key" />

MergeLocalConfiguration
***********************

Merges a local configuration downloaded from a remote TYPO3 CMS instance with the eventually changed local one.

Parameters

localfile (string)
   Path to file of local configuration
remotefile (string)
   Path to file of remote configuration

Usage:

.. code:: xml

   <mergelocalconfiguration
      localfile="../www/typo3conf/LocalConfiguration.php"
      remotefile="/tmp/LocalConfiguration.php" />