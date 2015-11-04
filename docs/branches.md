# Setting up Branches for Master, Develop and Local Environment
In order to have an environment for local testing, the environment for public
testing (**Develop**) and an environment for Stable Testing (**Master**), we
will follow the guidelines described in:

  http://nvie.com/posts/a-successful-git-branching-model/


## The Local Environment
The local environment needs the following tools:
* `git` for Version Control
* Latest `apache` version
* Latest `php` version
* Latest `mysql` or compatible fork version

In order to push changes to **develop** branch, it is needed to Configure the
GIT environment, just as described in
https://help.github.com/articles/set-up-git/

Then, you should create SSH keys for Server authentication, just as described
in https://help.github.com/articles/generating-ssh-keys/

TIP: There is no need to set a Key passphrasse.

Once done, you can clone the **develop** branch:

```sh
git clone -b develop git@github.com:SamanaGroup/NetPivot.git
```

Once that is done, you can make changes and local commits.

In order to push the changes to the remote **develop** branch:

```sh
git push origin develop
```

## Merge develop in master
The **develop** branch is using for Public testing only, and the **master**
branch is using for Stable Testing.

It is important to not commit changes to the **master** branch, as it is only
used for merging changes coming from **develop** branch.

