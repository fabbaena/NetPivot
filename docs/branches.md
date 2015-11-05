# Setting up Branches for Master, Develop and Local Environment
In order to have an environment for local testing, the environment for public
testing (**Develop**) and an environment for Stable Testing (**Master**), we
will follow the guidelines described in:

  http://nvie.com/posts/a-successful-git-branching-model/

## Process Flow
1. Get a **develop** branch local copy
2. Make changes
3. Commit changes
4. Push Changes to **origin develop**
5. Get a *Temporal* **master** branch local copy
6. Merge Changes from **origin/develop** in **No Fast Forward**
7. Push Changes to **origin master**


### Get a Develop branch local copy
The local environment needs the following tools:
* `git` for Version Control
* Latest `apache` version
* Latest `php` version
* Latest `mysql` or compatible fork version

In order to setup the Developer Identity, it is needed to Configure the GIT
environment, just as described in
https://help.github.com/articles/set-up-git/

Then, you should create SSH keys for Server authentication, just as described
in https://help.github.com/articles/generating-ssh-keys/

TIP: There is no need to set a Key passphrasse.

Once done, you can clone the **develop** branch:

```sh
git clone -b develop git@github.com:SamanaGroup/NetPivot.git develop/
```

Once that is done, you can make changes and local commits.

```sh
git checkout develop
git status
git add -v -A .
git commit -a
```

### Push changes to Origin Develop
In order to update the local copy and then push the changes to the remote
**develop** branch:

```sh
git checkout develop
git pull
git push origin develop
git pull
```

### Get a temporal Master branch local copy
The **develop** branch is using for Public testing only, and the **master**
branch is using for Stable Testing.

It is important to not commit changes to the **master** branch, as it is only
used for merging changes coming from **develop** branch.

TIP: You should separate **master** and **develop** in diferent directories to
     avoid changes confusions.

```sh
git clone -b master git@github.com:SamanaGroup/NetPivot.git master/
```

### Merge changes from Origin/Develop in No Fast Forward
To merge changes that are already pushed to **origin/develop** branch
(described in Step 4):

```sh
git checkout master
git pull
git merge --no-ff origin/develop
```

### Push Changes to Origin Master
To push the already merged changes to Repository Server:

```sh
git push origin master
```
The Stable Testing server will fetch the new changes from **master** branch
eventually.

TIP: It is important to keep the **master** local copy up to date or cleanly
clone it again.

## Public Testing Environment setup

