<?php
/****************************************************************
*****************************************************************
Copyright (C) 2003 Keith Ganger

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

You can find more information about GPL licence at:
http://www.gnu.org/licenses/gpl.html

****************************************************************
****************************************************************/
/**
*This class uses a bit mask to represent user permissions.
*This will allow you to store a singe integer representing a users permission set.
*You can then covert this integer back into an associative array with boolean
*values showing the users permission set.
*@author Keith Ganger <ganger2@adelphia.net>
* @version 1.0
* @since March 3 2004
*/

class SimpleBitmask
{
	/**
	 * This array is used to represent the users permission in usable format.
	 *You can change remove or add valuesto suit your needs.
	 *Just ensure that each element defaults to false. Once you have started storing
	 *users permsisions a change to the order of this array will cause the
	 *permissions to be incorectly interpreted.
	 * @type Associtive array
	 */
	var $options = array();


	public function __construct($values)
	{
		foreach ($values as $value) {
			$this->options[ $value ] = false;
		}
	}

	/**
	*This function will use an integer bitmask(as created by toBitmask())
	*to populate the class vaiable
	*$this->permissions with the users permissions as boolean values.
	*@param int $bitmask an integer representation of the users permisions.
	*This integer is created by toBitmask();
	*@return an associatve array with the users permissions.
	*/
	function getOptions($bitMask=0)
	{

		/*
		*The following explains how this code works.
		*
		*This table shows how bitmasks will represent a particular permission.
		*element bin number -- 2^i -- decimal equiv
		*read 00000001 -- 2^0 -- 1
		*write 00000010 -- 2^1 -- 2
		*delete 00000100 -- 2^2 -- 4
		*
		*
		*
		*The following code block loops through the permissions it uses a bitwise AND(&)
		*along with the terinary operator to assign the permissions.
		*You may want to visit the documentation at www.php.net on the pow function,
		*the terinary operator, and the bitwise AND(&).
		*When using the bitwise AND(&) all bits that are set in both $bitMask and the
		*return value of pow(2($i) are set to 1.
		*
		*For this example refer to the above table
		*A user with read and delete permissions would be represented as 00000101
		*which would be an integer bitmask of 5
		*when this code is executed the following would happen.
		*The bitmask of 5 would be passed into the function
		*so $bitmask is set to 5 or in binary(00000101).
		*The first time through the loop the function pow(2,0) returns 1 (00000001)
		*the bitwise AND(&) then compares
		*(00000001) to (00000101) and returns 00000001 this is not equal to 0
		*so the first element in the array "read" is set to true.
		*
		*The second time through the loop the function pow(2,1) returns 2 (00000010)
		*the bitwise AND(&) then compares
		*(00000010) to (00000101) and returns 00000000 this is equal to 0
		*so the second element in the array "write" is set to false.
		*
		**The second time through the loop the function pow(2,2) returns 4 (00000100)
		*the bitwise AND(&) then compares
		*(00000100) to (00000101) and returns 00000100 this is 4 and is not equal to 0
		*so the third element in the array "delete" is set to false.
		*
		*The code will loop through the remaining elements setting all of them to false.
		*/
		$i=0;
		foreach($this->options as $key=>$value){
			$this->options[$key]= (($bitMask & pow(2,$i)) !=0) ? true: false;
			//uncomment the next line if you would like to see what is happening.
			//echo $key . " i= ".strval($i)." power=" . strval(pow(2,$i)). "bitwise & = " . strval($bitMask & pow(2,$i))."<br>";
			$i++;
		}
		return $this->options;
	}


	/**This function will create and return and integer bitmask based on the permission values set in
	*the class variable $permissions. To use you would want to set the fields in $permissions to true for the permissions you want to grant.
	*Then call toBitmask() and store the integer value. Later you can pass that integer into getPermissions() to convert it back to an assoicative
	*array.
	*@return int an integer bitmask represeting the users permission set.
	*/
	function toBitmask()
	{
		$bitmask=0;
		$i=0;
		foreach($this->options as $key=>$value){

			if($value){
				$bitmask+=pow(2,$i);
			}
			$i++;
		}
		return $bitmask;
	}


}
