# Secure Box Audit Log

The Audit Log app for ownCloud

Provides an Audit Log
going on in your ownCloud.

## QA metrics on master branch:


## QA metrics on stable7 branch:


# Add new activities / types for other apps

With the activity manager extensions can be registered which allow any app to extend the activity behavior.

In order to implement an extension create a class which implements the interface `\OCP\Audit_log\IExtension`.

The PHPDoc comments on each method should give enough information to the developer on how to implement them.
