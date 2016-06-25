#!/bin/bash

export PGPASSFILE=/home/ubuntu/.pgpass
export PGHOST=localhost
export PGUSER=demonio
export PGPASSWORD=s3cur3s0c
export PGDATABASE=netpivot

echo "User cleanup, please select which user would you like to clean up."
echo "All files and conversions of this user will be deleted."

USERS=( `psql -w -c "SELECT name FROM users ORDER BY name ASC;"` )
FILES=/var/www/html/dashboard/files

select user in ${USERS[*]}; do
    USER_ID=`psql -w -c "SELECT id FROM users WHERE name = '$user' ORDER BY name ASC;"`
    FILE_UUID=( `psql -w -c "SELECT uuid FROM files WHERE users_id = '$USER_ID' ORDER BY uuid ASC;"` )
    for file in ${FILE_UUID[*]}; do
	echo -ne "\rDeleting details for file ($file)... "
	    psql -w -c "DELETE FROM details WHERE files_uuid = '$file';"
	echo -ne "Done"
	echo -ne "\rDeleting conversions for file ($file)... "
	    psql -w -c "DELETE FROM conversions WHERE files_uuid = '$file';"
	echo -ne "Done"
	echo -ne "\rDeleting files ($file)... "
	    psql -w -c "DELETE FROM files WHERE uuid = '$file';"
	    sudo rm -fr ${FILES}/$file*
	echo -ne "Done"
    done
    echo -ne "\nWould you like to delete the user too? (Y/N): "
    read answer
    if [ "$answer" == Y ] || [ "$answer" == y ]; then
	psql -w -c "DELETE FROM users WHERE name = '$user';"
	echo -ne "Done\n"
    fi
    echo "Finished."
    break;
done

