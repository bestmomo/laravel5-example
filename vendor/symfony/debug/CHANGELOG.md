CHANGELOG
=========

2.7.0
-----

* added deprecations checking for parent interfaces/classes to DebugClassLoader
* added ZTS support to symfony_debug extension
* added symfony_debug_backtrace() to symfony_debug extension
  to track the backtrace of fatal errors

2.6.0
-----

* generalized ErrorHandler and ExceptionHandler,
  with some new methods and others deprecated
* enhanced error messages for uncaught exceptions

2.5.0
-----

* added ExceptionHandler::setHandler()
* added UndefinedMethodFatalErrorHandler
* deprecated DummyException

2.4.0
-----

 * added a DebugClassLoader able to wrap any autoloader providing a findFile method
 * improved error messages for not found classes and functions

2.3.0
-----

 * added the component
