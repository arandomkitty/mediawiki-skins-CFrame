CFrame Skin
========================

Changes from Vector
------------
- Removed Vector 2022
- Replaced mentions of "Vector" with "CFrame"
- Added custom sidebar additions to ```SkinCFrame.php```
- Styled to look like the legacy Roblox website (moved and created elements in ```includes/templates```, default styling changes, added collapsible sidebar)

Installation
------------
Download and place the files in a folder named ```CFrame``` under the ```skins/``` folder.

Add the following two lines to LocalSettings.php
```
wfLoadSkin( 'CFrame' );
$wgDefaultSkin = "cframe";
```
