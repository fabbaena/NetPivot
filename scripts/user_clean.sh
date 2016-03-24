#!/bin/bash

echo "User cleanup, please select which user would you like to clean up."
echo "All files and conversions of this user will be deleted."

USERS=( `mysql -bsNL -e "SELECT name FROM NetPivot.users ORDER BY name ASC;"` )
FILES=/var/www/html/dashboard/files

select user in ${USERS[*]}; do
    USER_ID=`mysql -bsNL -e "SELECT id FROM NetPivot.users WHERE name = '$user' ORDER BY name ASC;"`
    FILE_UUID=( `mysql -bsNL -e "SELECT uuid FROM NetPivot.files WHERE users_id = '$USER_ID' ORDER BY uuid ASC;"` )
    for file in ${FILE_UUID[*]}; do
	echo -ne "\rDeleting details for file ($file)... "
	    mysql -bsNL -e "DELETE FROM NetPivot.details WHERE files_uuid = '$file';"
	echo -ne "Done"
	echo -ne "\rDeleting conversions for file ($file)... "
	    mysql -bsNL -e "DELETE FROM NetPivot.conversions WHERE files_uuid = '$file';"
	echo -ne "Done"
	echo -ne "\rDeleting files ($file)... "
	    mysql -bsNL -e "DELETE FROM NetPivot.files WHERE uuid = '$file';"
	    sudo rm -fr ${FILES}/$file*
	echo -ne "Done"
    done
    echo -ne "\nWould you like to delete the user too? (Y/N): "
    read answer
    if [ "$answer" == Y ] || [ "$answer" == y ]; then
	mysql -bsNL -e "DELETE FROM NetPivot.users WHERE name = '$user';"
	echo -ne "Done\n"
    fi
    echo "Finished."
    break;
done

