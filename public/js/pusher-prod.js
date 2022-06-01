var PusherPushNotifications = (function(exports) {
    'use strict';

    function _arrayWithoutHoles(arr) {
        if (Array.isArray(arr)) {
            for (var i = 0, arr2 = new Array(arr.length); i < arr.length; i++) {
                arr2[i] = arr[i];
            }

            return arr2;
        }
    }

    var arrayWithoutHoles = _arrayWithoutHoles;

    function _iterableToArray(iter) {
        if (Symbol.iterator in Object(iter) || Object.prototype.toString.call(iter) === "[object Arguments]") return Array.from(iter);
    }

    var iterableToArray = _iterableToArray;

    function _nonIterableSpread() {
        throw new TypeError("Invalid attempt to spread non-iterable instance");
    }

    var nonIterableSpread = _nonIterableSpread;

    function _toConsumableArray(arr) {
        return arrayWithoutHoles(arr) || iterableToArray(arr) || nonIterableSpread();
    }

    var toConsumableArray = _toConsumableArray;

    function createCommonjsModule(fn, module) {
        return module = { exports: {} }, fn(module, module.exports), module.exports;
    }

    var runtime_1 = createCommonjsModule(function(module) {
        /**
         * Copyright (c) 2014-present, Facebook, Inc.
         *
         * This source code is licensed under the MIT license found in the
         * LICENSE file in the root directory of this source tree.
         */

        var runtime = (function(exports) {

            var Op = Object.prototype;
            var hasOwn = Op.hasOwnProperty;
            var undefined$1; // More compressible than void 0.
            var $Symbol = typeof Symbol === "function" ? Symbol : {};
            var iteratorSymbol = $Symbol.iterator || "@@iterator";
            var asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator";
            var toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

            function wrap(innerFn, outerFn, self, tryLocsList) {
                // If outerFn provided and outerFn.prototype is a Generator, then outerFn.prototype instanceof Generator.
                var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator;
                var generator = Object.create(protoGenerator.prototype);
                var context = new Context(tryLocsList || []);

                // The ._invoke method unifies the implementations of the .next,
                // .throw, and .return methods.
                generator._invoke = makeInvokeMethod(innerFn, self, context);

                return generator;
            }
            exports.wrap = wrap;

            // Try/catch helper to minimize deoptimizations. Returns a completion
            // record like context.tryEntries[i].completion. This interface could
            // have been (and was previously) designed to take a closure to be
            // invoked without arguments, but in all the cases we care about we
            // already have an existing method we want to call, so there's no need
            // to create a new function object. We can even get away with assuming
            // the method takes exactly one argument, since that happens to be true
            // in every case, so we don't have to touch the arguments object. The
            // only additional allocation required is the completion record, which
            // has a stable shape and so hopefully should be cheap to allocate.
            function tryCatch(fn, obj, arg) {
                try {
                    return { type: "normal", arg: fn.call(obj, arg) };
                } catch (err) {
                    return { type: "throw", arg: err };
                }
            }

            var GenStateSuspendedStart = "suspendedStart";
            var GenStateSuspendedYield = "suspendedYield";
            var GenStateExecuting = "executing";
            var GenStateCompleted = "completed";

            // Returning this object from the innerFn has the same effect as
            // breaking out of the dispatch switch statement.
            var ContinueSentinel = {};

            // Dummy constructor functions that we use as the .constructor and
            // .constructor.prototype properties for functions that return Generator
            // objects. For full spec compliance, you may wish to configure your
            // minifier not to mangle the names of these two functions.
            function Generator() {}

            function GeneratorFunction() {}

            function GeneratorFunctionPrototype() {}

            // This is a polyfill for %IteratorPrototype% for environments that
            // don't natively support it.
            var IteratorPrototype = {};
            IteratorPrototype[iteratorSymbol] = function() {
                return this;
            };

            var getProto = Object.getPrototypeOf;
            var NativeIteratorPrototype = getProto && getProto(getProto(values([])));
            if (NativeIteratorPrototype &&
                NativeIteratorPrototype !== Op &&
                hasOwn.call(NativeIteratorPrototype, iteratorSymbol)) {
                // This environment has a native %IteratorPrototype%; use it instead
                // of the polyfill.
                IteratorPrototype = NativeIteratorPrototype;
            }

            var Gp = GeneratorFunctionPrototype.prototype =
                Generator.prototype = Object.create(IteratorPrototype);
            GeneratorFunction.prototype = Gp.constructor = GeneratorFunctionPrototype;
            GeneratorFunctionPrototype.constructor = GeneratorFunction;
            GeneratorFunctionPrototype[toStringTagSymbol] =
                GeneratorFunction.displayName = "GeneratorFunction";

            // Helper for defining the .next, .throw, and .return methods of the
            // Iterator interface in terms of a single ._invoke method.
            function defineIteratorMethods(prototype) {
                ["next", "throw", "return"].forEach(function(method) {
                    prototype[method] = function(arg) {
                        return this._invoke(method, arg);
                    };
                });
            }

            exports.isGeneratorFunction = function(genFun) {
                var ctor = typeof genFun === "function" && genFun.constructor;
                return ctor ?
                    ctor === GeneratorFunction ||
                    // For the native GeneratorFunction constructor, the best we can
                    // do is to check its .name property.
                    (ctor.displayName || ctor.name) === "GeneratorFunction" :
                    false;
            };

            exports.mark = function(genFun) {
                if (Object.setPrototypeOf) {
                    Object.setPrototypeOf(genFun, GeneratorFunctionPrototype);
                } else {
                    genFun.__proto__ = GeneratorFunctionPrototype;
                    if (!(toStringTagSymbol in genFun)) {
                        genFun[toStringTagSymbol] = "GeneratorFunction";
                    }
                }
                genFun.prototype = Object.create(Gp);
                return genFun;
            };

            // Within the body of any async function, `await x` is transformed to
            // `yield regeneratorRuntime.awrap(x)`, so that the runtime can test
            // `hasOwn.call(value, "__await")` to determine if the yielded value is
            // meant to be awaited.
            exports.awrap = function(arg) {
                return { __await: arg };
            };

            function AsyncIterator(generator) {
                function invoke(method, arg, resolve, reject) {
                    var record = tryCatch(generator[method], generator, arg);
                    if (record.type === "throw") {
                        reject(record.arg);
                    } else {
                        var result = record.arg;
                        var value = result.value;
                        if (value &&
                            typeof value === "object" &&
                            hasOwn.call(value, "__await")) {
                            return Promise.resolve(value.__await).then(function(value) {
                                invoke("next", value, resolve, reject);
                            }, function(err) {
                                invoke("throw", err, resolve, reject);
                            });
                        }

                        return Promise.resolve(value).then(function(unwrapped) {
                            // When a yielded Promise is resolved, its final value becomes
                            // the .value of the Promise<{value,done}> result for the
                            // current iteration.
                            result.value = unwrapped;
                            resolve(result);
                        }, function(error) {
                            // If a rejected Promise was yielded, throw the rejection back
                            // into the async generator function so it can be handled there.
                            return invoke("throw", error, resolve, reject);
                        });
                    }
                }

                var previousPromise;

                function enqueue(method, arg) {
                    function callInvokeWithMethodAndArg() {
                        return new Promise(function(resolve, reject) {
                            invoke(method, arg, resolve, reject);
                        });
                    }

                    return previousPromise =
                        // If enqueue has been called before, then we want to wait until
                        // all previous Promises have been resolved before calling invoke,
                        // so that results are always delivered in the correct order. If
                        // enqueue has not been called before, then it is important to
                        // call invoke immediately, without waiting on a callback to fire,
                        // so that the async generator function has the opportunity to do
                        // any necessary setup in a predictable way. This predictability
                        // is why the Promise constructor synchronously invokes its
                        // executor callback, and why async functions synchronously
                        // execute code before the first await. Since we implement simple
                        // async functions in terms of async generators, it is especially
                        // important to get this right, even though it requires care.
                        previousPromise ? previousPromise.then(
                            callInvokeWithMethodAndArg,
                            // Avoid propagating failures to Promises returned by later
                            // invocations of the iterator.
                            callInvokeWithMethodAndArg
                        ) : callInvokeWithMethodAndArg();
                }

                // Define the unified helper method that is used to implement .next,
                // .throw, and .return (see defineIteratorMethods).
                this._invoke = enqueue;
            }

            defineIteratorMethods(AsyncIterator.prototype);
            AsyncIterator.prototype[asyncIteratorSymbol] = function() {
                return this;
            };
            exports.AsyncIterator = AsyncIterator;

            // Note that simple async functions are implemented on top of
            // AsyncIterator objects; they just return a Promise for the value of
            // the final result produced by the iterator.
            exports.async = function(innerFn, outerFn, self, tryLocsList) {
                var iter = new AsyncIterator(
                    wrap(innerFn, outerFn, self, tryLocsList)
                );

                return exports.isGeneratorFunction(outerFn) ?
                    iter // If outerFn is a generator, return the full iterator.
                    :
                    iter.next().then(function(result) {
                        return result.done ? result.value : iter.next();
                    });
            };

            function makeInvokeMethod(innerFn, self, context) {
                var state = GenStateSuspendedStart;

                return function invoke(method, arg) {
                    if (state === GenStateExecuting) {
                        throw new Error("Generator is already running");
                    }

                    if (state === GenStateCompleted) {
                        if (method === "throw") {
                            throw arg;
                        }

                        // Be forgiving, per 25.3.3.3.3 of the spec:
                        // https://people.mozilla.org/~jorendorff/es6-draft.html#sec-generatorresume
                        return doneResult();
                    }

                    context.method = method;
                    context.arg = arg;

                    while (true) {
                        var delegate = context.delegate;
                        if (delegate) {
                            var delegateResult = maybeInvokeDelegate(delegate, context);
                            if (delegateResult) {
                                if (delegateResult === ContinueSentinel) continue;
                                return delegateResult;
                            }
                        }

                        if (context.method === "next") {
                            // Setting context._sent for legacy support of Babel's
                            // function.sent implementation.
                            context.sent = context._sent = context.arg;

                        } else if (context.method === "throw") {
                            if (state === GenStateSuspendedStart) {
                                state = GenStateCompleted;
                                throw context.arg;
                            }

                            context.dispatchException(context.arg);

                        } else if (context.method === "return") {
                            context.abrupt("return", context.arg);
                        }

                        state = GenStateExecuting;

                        var record = tryCatch(innerFn, self, context);
                        if (record.type === "normal") {
                            // If an exception is thrown from innerFn, we leave state ===
                            // GenStateExecuting and loop back for another invocation.
                            state = context.done ?
                                GenStateCompleted :
                                GenStateSuspendedYield;

                            if (record.arg === ContinueSentinel) {
                                continue;
                            }

                            return {
                                value: record.arg,
                                done: context.done
                            };

                        } else if (record.type === "throw") {
                            state = GenStateCompleted;
                            // Dispatch the exception by looping back around to the
                            // context.dispatchException(context.arg) call above.
                            context.method = "throw";
                            context.arg = record.arg;
                        }
                    }
                };
            }

            // Call delegate.iterator[context.method](context.arg) and handle the
            // result, either by returning a { value, done } result from the
            // delegate iterator, or by modifying context.method and context.arg,
            // setting context.delegate to null, and returning the ContinueSentinel.
            function maybeInvokeDelegate(delegate, context) {
                var method = delegate.iterator[context.method];
                if (method === undefined$1) {
                    // A .throw or .return when the delegate iterator has no .throw
                    // method always terminates the yield* loop.
                    context.delegate = null;

                    if (context.method === "throw") {
                        // Note: ["return"] must be used for ES3 parsing compatibility.
                        if (delegate.iterator["return"]) {
                            // If the delegate iterator has a return method, give it a
                            // chance to clean up.
                            context.method = "return";
                            context.arg = undefined$1;
                            maybeInvokeDelegate(delegate, context);

                            if (context.method === "throw") {
                                // If maybeInvokeDelegate(context) changed context.method from
                                // "return" to "throw", let that override the TypeError below.
                                return ContinueSentinel;
                            }
                        }

                        context.method = "throw";
                        context.arg = new TypeError(
                            "The iterator does not provide a 'throw' method");
                    }

                    return ContinueSentinel;
                }

                var record = tryCatch(method, delegate.iterator, context.arg);

                if (record.type === "throw") {
                    context.method = "throw";
                    context.arg = record.arg;
                    context.delegate = null;
                    return ContinueSentinel;
                }

                var info = record.arg;

                if (!info) {
                    context.method = "throw";
                    context.arg = new TypeError("iterator result is not an object");
                    context.delegate = null;
                    return ContinueSentinel;
                }

                if (info.done) {
                    // Assign the result of the finished delegate to the temporary
                    // variable specified by delegate.resultName (see delegateYield).
                    context[delegate.resultName] = info.value;

                    // Resume execution at the desired location (see delegateYield).
                    context.next = delegate.nextLoc;

                    // If context.method was "throw" but the delegate handled the
                    // exception, let the outer generator proceed normally. If
                    // context.method was "next", forget context.arg since it has been
                    // "consumed" by the delegate iterator. If context.method was
                    // "return", allow the original .return call to continue in the
                    // outer generator.
                    if (context.method !== "return") {
                        context.method = "next";
                        context.arg = undefined$1;
                    }

                } else {
                    // Re-yield the result returned by the delegate method.
                    return info;
                }

                // The delegate iterator is finished, so forget it and continue with
                // the outer generator.
                context.delegate = null;
                return ContinueSentinel;
            }

            // Define Generator.prototype.{next,throw,return} in terms of the
            // unified ._invoke helper method.
            defineIteratorMethods(Gp);

            Gp[toStringTagSymbol] = "Generator";

            // A Generator should always return itself as the iterator object when the
            // @@iterator function is called on it. Some browsers' implementations of the
            // iterator prototype chain incorrectly implement this, causing the Generator
            // object to not be returned from this call. This ensures that doesn't happen.
            // See https://github.com/facebook/regenerator/issues/274 for more details.
            Gp[iteratorSymbol] = function() {
                return this;
            };

            Gp.toString = function() {
                return "[object Generator]";
            };

            function pushTryEntry(locs) {
                var entry = { tryLoc: locs[0] };

                if (1 in locs) {
                    entry.catchLoc = locs[1];
                }

                if (2 in locs) {
                    entry.finallyLoc = locs[2];
                    entry.afterLoc = locs[3];
                }

                this.tryEntries.push(entry);
            }

            function resetTryEntry(entry) {
                var record = entry.completion || {};
                record.type = "normal";
                delete record.arg;
                entry.completion = record;
            }

            function Context(tryLocsList) {
                // The root entry object (effectively a try statement without a catch
                // or a finally block) gives us a place to store values thrown from
                // locations where there is no enclosing try statement.
                this.tryEntries = [{ tryLoc: "root" }];
                tryLocsList.forEach(pushTryEntry, this);
                this.reset(true);
            }

            exports.keys = function(object) {
                var keys = [];
                for (var key in object) {
                    keys.push(key);
                }
                keys.reverse();

                // Rather than returning an object with a next method, we keep
                // things simple and return the next function itself.
                return function next() {
                    while (keys.length) {
                        var key = keys.pop();
                        if (key in object) {
                            next.value = key;
                            next.done = false;
                            return next;
                        }
                    }

                    // To avoid creating an additional object, we just hang the .value
                    // and .done properties off the next function object itself. This
                    // also ensures that the minifier will not anonymize the function.
                    next.done = true;
                    return next;
                };
            };

            function values(iterable) {
                if (iterable) {
                    var iteratorMethod = iterable[iteratorSymbol];
                    if (iteratorMethod) {
                        return iteratorMethod.call(iterable);
                    }

                    if (typeof iterable.next === "function") {
                        return iterable;
                    }

                    if (!isNaN(iterable.length)) {
                        var i = -1,
                            next = function next() {
                                while (++i < iterable.length) {
                                    if (hasOwn.call(iterable, i)) {
                                        next.value = iterable[i];
                                        next.done = false;
                                        return next;
                                    }
                                }

                                next.value = undefined$1;
                                next.done = true;

                                return next;
                            };

                        return next.next = next;
                    }
                }

                // Return an iterator with no values.
                return { next: doneResult };
            }
            exports.values = values;

            function doneResult() {
                return { value: undefined$1, done: true };
            }

            Context.prototype = {
                constructor: Context,

                reset: function(skipTempReset) {
                    this.prev = 0;
                    this.next = 0;
                    // Resetting context._sent for legacy support of Babel's
                    // function.sent implementation.
                    this.sent = this._sent = undefined$1;
                    this.done = false;
                    this.delegate = null;

                    this.method = "next";
                    this.arg = undefined$1;

                    this.tryEntries.forEach(resetTryEntry);

                    if (!skipTempReset) {
                        for (var name in this) {
                            // Not sure about the optimal order of these conditions:
                            if (name.charAt(0) === "t" &&
                                hasOwn.call(this, name) &&
                                !isNaN(+name.slice(1))) {
                                this[name] = undefined$1;
                            }
                        }
                    }
                },

                stop: function() {
                    this.done = true;

                    var rootEntry = this.tryEntries[0];
                    var rootRecord = rootEntry.completion;
                    if (rootRecord.type === "throw") {
                        throw rootRecord.arg;
                    }

                    return this.rval;
                },

                dispatchException: function(exception) {
                    if (this.done) {
                        throw exception;
                    }

                    var context = this;

                    function handle(loc, caught) {
                        record.type = "throw";
                        record.arg = exception;
                        context.next = loc;

                        if (caught) {
                            // If the dispatched exception was caught by a catch block,
                            // then let that catch block handle the exception normally.
                            context.method = "next";
                            context.arg = undefined$1;
                        }

                        return !!caught;
                    }

                    for (var i = this.tryEntries.length - 1; i >= 0; --i) {
                        var entry = this.tryEntries[i];
                        var record = entry.completion;

                        if (entry.tryLoc === "root") {
                            // Exception thrown outside of any try block that could handle
                            // it, so set the completion value of the entire function to
                            // throw the exception.
                            return handle("end");
                        }

                        if (entry.tryLoc <= this.prev) {
                            var hasCatch = hasOwn.call(entry, "catchLoc");
                            var hasFinally = hasOwn.call(entry, "finallyLoc");

                            if (hasCatch && hasFinally) {
                                if (this.prev < entry.catchLoc) {
                                    return handle(entry.catchLoc, true);
                                } else if (this.prev < entry.finallyLoc) {
                                    return handle(entry.finallyLoc);
                                }

                            } else if (hasCatch) {
                                if (this.prev < entry.catchLoc) {
                                    return handle(entry.catchLoc, true);
                                }

                            } else if (hasFinally) {
                                if (this.prev < entry.finallyLoc) {
                                    return handle(entry.finallyLoc);
                                }

                            } else {
                                throw new Error("try statement without catch or finally");
                            }
                        }
                    }
                },

                abrupt: function(type, arg) {
                    for (var i = this.tryEntries.length - 1; i >= 0; --i) {
                        var entry = this.tryEntries[i];
                        if (entry.tryLoc <= this.prev &&
                            hasOwn.call(entry, "finallyLoc") &&
                            this.prev < entry.finallyLoc) {
                            var finallyEntry = entry;
                            break;
                        }
                    }

                    if (finallyEntry &&
                        (type === "break" ||
                            type === "continue") &&
                        finallyEntry.tryLoc <= arg &&
                        arg <= finallyEntry.finallyLoc) {
                        // Ignore the finally entry if control is not jumping to a
                        // location outside the try/catch block.
                        finallyEntry = null;
                    }

                    var record = finallyEntry ? finallyEntry.completion : {};
                    record.type = type;
                    record.arg = arg;

                    if (finallyEntry) {
                        this.method = "next";
                        this.next = finallyEntry.finallyLoc;
                        return ContinueSentinel;
                    }

                    return this.complete(record);
                },

                complete: function(record, afterLoc) {
                    if (record.type === "throw") {
                        throw record.arg;
                    }

                    if (record.type === "break" ||
                        record.type === "continue") {
                        this.next = record.arg;
                    } else if (record.type === "return") {
                        this.rval = this.arg = record.arg;
                        this.method = "return";
                        this.next = "end";
                    } else if (record.type === "normal" && afterLoc) {
                        this.next = afterLoc;
                    }

                    return ContinueSentinel;
                },

                finish: function(finallyLoc) {
                    for (var i = this.tryEntries.length - 1; i >= 0; --i) {
                        var entry = this.tryEntries[i];
                        if (entry.finallyLoc === finallyLoc) {
                            this.complete(entry.completion, entry.afterLoc);
                            resetTryEntry(entry);
                            return ContinueSentinel;
                        }
                    }
                },

                "catch": function(tryLoc) {
                    for (var i = this.tryEntries.length - 1; i >= 0; --i) {
                        var entry = this.tryEntries[i];
                        if (entry.tryLoc === tryLoc) {
                            var record = entry.completion;
                            if (record.type === "throw") {
                                var thrown = record.arg;
                                resetTryEntry(entry);
                            }
                            return thrown;
                        }
                    }

                    // The context.catch method must only be called with a location
                    // argument that corresponds to a known catch block.
                    throw new Error("illegal catch attempt");
                },

                delegateYield: function(iterable, resultName, nextLoc) {
                    this.delegate = {
                        iterator: values(iterable),
                        resultName: resultName,
                        nextLoc: nextLoc
                    };

                    if (this.method === "next") {
                        // Deliberately forget the last sent value so that we don't
                        // accidentally pass it on to the delegate.
                        this.arg = undefined$1;
                    }

                    return ContinueSentinel;
                }
            };

            // Regardless of whether this script is executing as a CommonJS module
            // or not, return the runtime object so that we can declare the variable
            // regeneratorRuntime in the outer scope, which allows this module to be
            // injected easily by `bin/regenerator --include-runtime script.js`.
            return exports;

        }(
            // If this script is executing as a CommonJS module, use module.exports
            // as the regeneratorRuntime namespace. Otherwise create a new empty
            // object. Either way, the resulting object will be used to initialize
            // the regeneratorRuntime variable at the top of this file.
            module.exports
        ));

        try {
            regeneratorRuntime = runtime;
        } catch (accidentalStrictMode) {
            // This module should not be running in strict mode, so the above
            // assignment should always work unless something is misconfigured. Just
            // in case runtime.js accidentally runs in strict mode, we can escape
            // strict mode using a global Function call. This could conceivably fail
            // if a Content Security Policy forbids using Function, but in that case
            // the proper solution is to fix the accidental strict mode problem. If
            // you've misconfigured your bundler to force strict mode and applied a
            // CSP to forbid Function, and you're not willing to fix either of those
            // problems, please detail your unique predicament in a GitHub issue.
            Function("r", "regeneratorRuntime = r")(runtime);
        }
    });

    var regenerator = runtime_1;

    function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
        try {
            var info = gen[key](arg);
            var value = info.value;
        } catch (error) {
            reject(error);
            return;
        }

        if (info.done) {
            resolve(value);
        } else {
            Promise.resolve(value).then(_next, _throw);
        }
    }

    function _asyncToGenerator(fn) {
        return function() {
            var self = this,
                args = arguments;
            return new Promise(function(resolve, reject) {
                var gen = fn.apply(self, args);

                function _next(value) {
                    asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
                }

                function _throw(err) {
                    asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
                }

                _next(undefined);
            });
        };
    }

    var asyncToGenerator = _asyncToGenerator;

    function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
            throw new TypeError("Cannot call a class as a function");
        }
    }

    var classCallCheck = _classCallCheck;

    function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
            var descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
        }
    }

    function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps) _defineProperties(Constructor.prototype, protoProps);
        if (staticProps) _defineProperties(Constructor, staticProps);
        return Constructor;
    }

    var createClass = _createClass;

    var _typeof_1 = createCommonjsModule(function(module) {
        function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

        function _typeof(obj) {
            if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
                module.exports = _typeof = function _typeof(obj) {
                    return _typeof2(obj);
                };
            } else {
                module.exports = _typeof = function _typeof(obj) {
                    return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
                };
            }

            return _typeof(obj);
        }

        module.exports = _typeof;
    });

    function _assertThisInitialized(self) {
        if (self === void 0) {
            throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
        }

        return self;
    }

    var assertThisInitialized = _assertThisInitialized;

    function _possibleConstructorReturn(self, call) {
        if (call && (_typeof_1(call) === "object" || typeof call === "function")) {
            return call;
        }

        return assertThisInitialized(self);
    }

    var possibleConstructorReturn = _possibleConstructorReturn;

    var getPrototypeOf = createCommonjsModule(function(module) {
        function _getPrototypeOf(o) {
            module.exports = _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
                return o.__proto__ || Object.getPrototypeOf(o);
            };
            return _getPrototypeOf(o);
        }

        module.exports = _getPrototypeOf;
    });

    function _superPropBase(object, property) {
        while (!Object.prototype.hasOwnProperty.call(object, property)) {
            object = getPrototypeOf(object);
            if (object === null) break;
        }

        return object;
    }

    var superPropBase = _superPropBase;

    var get = createCommonjsModule(function(module) {
        function _get(target, property, receiver) {
            if (typeof Reflect !== "undefined" && Reflect.get) {
                module.exports = _get = Reflect.get;
            } else {
                module.exports = _get = function _get(target, property, receiver) {
                    var base = superPropBase(target, property);
                    if (!base) return;
                    var desc = Object.getOwnPropertyDescriptor(base, property);

                    if (desc.get) {
                        return desc.get.call(receiver);
                    }

                    return desc.value;
                };
            }

            return _get(target, property, receiver || target);
        }

        module.exports = _get;
    });

    var setPrototypeOf = createCommonjsModule(function(module) {
        function _setPrototypeOf(o, p) {
            module.exports = _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
                o.__proto__ = p;
                return o;
            };

            return _setPrototypeOf(o, p);
        }

        module.exports = _setPrototypeOf;
    });

    function _inherits(subClass, superClass) {
        if (typeof superClass !== "function" && superClass !== null) {
            throw new TypeError("Super expression must either be null or a function");
        }

        subClass.prototype = Object.create(superClass && superClass.prototype, {
            constructor: {
                value: subClass,
                writable: true,
                configurable: true
            }
        });
        if (superClass) setPrototypeOf(subClass, superClass);
    }

    var inherits = _inherits;

    function _defineProperty(obj, key, value) {
        if (key in obj) {
            Object.defineProperty(obj, key, {
                value: value,
                enumerable: true,
                configurable: true,
                writable: true
            });
        } else {
            obj[key] = value;
        }

        return obj;
    }

    var defineProperty = _defineProperty;

    function _objectSpread(target) {
        for (var i = 1; i < arguments.length; i++) {
            var source = arguments[i] != null ? arguments[i] : {};
            var ownKeys = Object.keys(source);

            if (typeof Object.getOwnPropertySymbols === 'function') {
                ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function(sym) {
                    return Object.getOwnPropertyDescriptor(source, sym).enumerable;
                }));
            }

            ownKeys.forEach(function(key) {
                defineProperty(target, key, source[key]);
            });
        }

        return target;
    }

    var objectSpread = _objectSpread;

    function doRequest(_ref) {
        var method = _ref.method,
            path = _ref.path,
            _ref$body = _ref.body,
            body = _ref$body === void 0 ? null : _ref$body,
            _ref$headers = _ref.headers,
            headers = _ref$headers === void 0 ? {} : _ref$headers,
            _ref$credentials = _ref.credentials,
            credentials = _ref$credentials === void 0 ? 'same-origin' : _ref$credentials;
        var options = {
            method: method,
            headers: headers,
            credentials: credentials
        };

        if (body !== null) {
            options.body = JSON.stringify(body);
            options.headers = objectSpread({
                'Content-Type': 'application/json'
            }, headers);
        }

        return fetch(path, options).then(
            /*#__PURE__*/
            function() {
                var _ref2 = asyncToGenerator(
                    /*#__PURE__*/
                    regenerator.mark(function _callee(response) {
                        return regenerator.wrap(function _callee$(_context) {
                            while (1) {
                                switch (_context.prev = _context.next) {
                                    case 0:
                                        if (response.ok) {
                                            _context.next = 3;
                                            break;
                                        }

                                        _context.next = 3;
                                        return handleError(response);

                                    case 3:
                                        _context.prev = 3;
                                        _context.next = 6;
                                        return response.json();

                                    case 6:
                                        return _context.abrupt("return", _context.sent);

                                    case 9:
                                        _context.prev = 9;
                                        _context.t0 = _context["catch"](3);
                                        return _context.abrupt("return", null);

                                    case 12:
                                    case "end":
                                        return _context.stop();
                                }
                            }
                        }, _callee, null, [
                            [3, 9]
                        ]);
                    }));

                return function(_x) {
                    return _ref2.apply(this, arguments);
                };
            }());
    }

    function handleError(_x2) {
        return _handleError.apply(this, arguments);
    }

    function _handleError() {
        _handleError = asyncToGenerator(
            /*#__PURE__*/
            regenerator.mark(function _callee2(response) {
                var errorMessage, _ref3, _ref3$error, error, _ref3$description, description;

                return regenerator.wrap(function _callee2$(_context2) {
                    while (1) {
                        switch (_context2.prev = _context2.next) {
                            case 0:
                                _context2.prev = 0;
                                _context2.next = 3;
                                return response.json();

                            case 3:
                                _ref3 = _context2.sent;
                                _ref3$error = _ref3.error;
                                error = _ref3$error === void 0 ? 'Unknown error' : _ref3$error;
                                _ref3$description = _ref3.description;
                                description = _ref3$description === void 0 ? 'No description' : _ref3$description;
                                errorMessage = "Unexpected status code ".concat(response.status, ": ").concat(error, ", ").concat(description);
                                _context2.next = 14;
                                break;

                            case 11:
                                _context2.prev = 11;
                                _context2.t0 = _context2["catch"](0);
                                errorMessage = "Unexpected status code ".concat(response.status, ": Cannot parse error response");

                            case 14:
                                throw new Error(errorMessage);

                            case 15:
                            case "end":
                                return _context2.stop();
                        }
                    }
                }, _callee2, null, [
                    [0, 11]
                ]);
            }));
        return _handleError.apply(this, arguments);
    }

    var version = "2.0.0-beta.1";

    var DeviceStateStore =
        /*#__PURE__*/
        function() {
            function DeviceStateStore(instanceId) {
                classCallCheck(this, DeviceStateStore);

                this._instanceId = instanceId;
                this._dbConn = null;
            }

            createClass(DeviceStateStore, [{
                key: "connect",
                value: function connect() {
                    var _this = this;

                    return new Promise(function(resolve, reject) {
                        var request = indexedDB.open(_this._dbName);

                        request.onsuccess = function(event) {
                            var db = event.target.result;
                            _this._dbConn = db;

                            _this._readState().then(function(state) {
                                return state === null ? _this.clear() : Promise.resolve();
                            }).then(resolve);
                        };

                        request.onupgradeneeded = function(event) {
                            var db = event.target.result;
                            db.createObjectStore('beams', {
                                keyPath: 'instance_id'
                            });
                        };

                        request.onerror = function(event) {
                            var error = new Error("Database error: ".concat(event.target.error));
                            reject(error);
                        };
                    });
                }
            }, {
                key: "clear",
                value: function clear() {
                    return this._writeState({
                        instance_id: this._instanceId,
                        device_id: null,
                        token: null,
                        user_id: null
                    });
                }
            }, {
                key: "_readState",
                value: function _readState() {
                    var _this2 = this;

                    if (!this.isConnected) {
                        throw new Error('Cannot read value: DeviceStateStore not connected to IndexedDB');
                    }

                    return new Promise(function(resolve, reject) {
                        var request = _this2._dbConn.transaction('beams').objectStore('beams').get(_this2._instanceId);

                        request.onsuccess = function(event) {
                            var state = event.target.result;

                            if (!state) {
                                resolve(null);
                            }

                            resolve(state);
                        };

                        request.onerror = function(event) {
                            reject(event.target.error);
                        };
                    });
                }
            }, {
                key: "_readProperty",
                value: function() {
                    var _readProperty2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee(name) {
                            var state;
                            return regenerator.wrap(function _callee$(_context) {
                                while (1) {
                                    switch (_context.prev = _context.next) {
                                        case 0:
                                            _context.next = 2;
                                            return this._readState();

                                        case 2:
                                            state = _context.sent;

                                            if (!(state === null)) {
                                                _context.next = 5;
                                                break;
                                            }

                                            return _context.abrupt("return", null);

                                        case 5:
                                            return _context.abrupt("return", state[name] || null);

                                        case 6:
                                        case "end":
                                            return _context.stop();
                                    }
                                }
                            }, _callee, this);
                        }));

                    function _readProperty(_x) {
                        return _readProperty2.apply(this, arguments);
                    }

                    return _readProperty;
                }()
            }, {
                key: "_writeState",
                value: function _writeState(state) {
                    var _this3 = this;

                    if (!this.isConnected) {
                        throw new Error('Cannot write value: DeviceStateStore not connected to IndexedDB');
                    }

                    return new Promise(function(resolve, reject) {
                        var request = _this3._dbConn.transaction('beams', 'readwrite').objectStore('beams').put(state);

                        request.onsuccess = function(_) {
                            resolve();
                        };

                        request.onerror = function(event) {
                            reject(event.target.error);
                        };
                    });
                }
            }, {
                key: "_writeProperty",
                value: function() {
                    var _writeProperty2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee2(name, value) {
                            var state;
                            return regenerator.wrap(function _callee2$(_context2) {
                                while (1) {
                                    switch (_context2.prev = _context2.next) {
                                        case 0:
                                            _context2.next = 2;
                                            return this._readState();

                                        case 2:
                                            state = _context2.sent;
                                            state[name] = value;
                                            _context2.next = 6;
                                            return this._writeState(state);

                                        case 6:
                                        case "end":
                                            return _context2.stop();
                                    }
                                }
                            }, _callee2, this);
                        }));

                    function _writeProperty(_x2, _x3) {
                        return _writeProperty2.apply(this, arguments);
                    }

                    return _writeProperty;
                }()
            }, {
                key: "getToken",
                value: function getToken() {
                    return this._readProperty('token');
                }
            }, {
                key: "setToken",
                value: function setToken(token) {
                    return this._writeProperty('token', token);
                }
            }, {
                key: "getDeviceId",
                value: function getDeviceId() {
                    return this._readProperty('device_id');
                }
            }, {
                key: "setDeviceId",
                value: function setDeviceId(deviceId) {
                    return this._writeProperty('device_id', deviceId);
                }
            }, {
                key: "getUserId",
                value: function getUserId() {
                    return this._readProperty('user_id');
                }
            }, {
                key: "setUserId",
                value: function setUserId(userId) {
                    return this._writeProperty('user_id', userId);
                }
            }, {
                key: "getLastSeenSdkVersion",
                value: function getLastSeenSdkVersion() {
                    return this._readProperty('last_seen_sdk_version');
                }
            }, {
                key: "setLastSeenSdkVersion",
                value: function setLastSeenSdkVersion(sdkVersion) {
                    return this._writeProperty('last_seen_sdk_version', sdkVersion);
                }
            }, {
                key: "getLastSeenUserAgent",
                value: function getLastSeenUserAgent() {
                    return this._readProperty('last_seen_user_agent');
                }
            }, {
                key: "setLastSeenUserAgent",
                value: function setLastSeenUserAgent(userAgent) {
                    return this._writeProperty('last_seen_user_agent', userAgent);
                }
            }, {
                key: "_dbName",
                get: function get() {
                    return "beams-".concat(this._instanceId);
                }
            }, {
                key: "isConnected",
                get: function get() {
                    return this._dbConn !== null;
                }
            }]);

            return DeviceStateStore;
        }();

    var INTERESTS_REGEX = new RegExp('^(_|\\-|=|@|,|\\.|;|[A-Z]|[a-z]|[0-9])*$');
    var MAX_INTEREST_LENGTH = 164;
    var MAX_INTERESTS_NUM = 5000;
    var RegistrationState = Object.freeze({
        PERMISSION_PROMPT_REQUIRED: 'PERMISSION_PROMPT_REQUIRED',
        PERMISSION_GRANTED_NOT_REGISTERED_WITH_BEAMS: 'PERMISSION_GRANTED_NOT_REGISTERED_WITH_BEAMS',
        PERMISSION_GRANTED_REGISTERED_WITH_BEAMS: 'PERMISSION_GRANTED_REGISTERED_WITH_BEAMS',
        PERMISSION_DENIED: 'PERMISSION_DENIED'
    });
    /* BaseClient is an abstract client containing functionality shared between
     * safari and web push clients. Platform specific classes should extend this
     * class. This method expects sub classes to implement the following public
     * methods:
     * async start()
     * async getRegistrationState() {
     * async stop() {
     * async clearAllState() {
     *
     * It also assumes that the following private methods are implemented:
     * async _init()
     * async _detectSubscriptionChange()
     */

    var BaseClient =
        /*#__PURE__*/
        function() {
            function BaseClient(config, platform) {
                classCallCheck(this, BaseClient);

                if (this.constructor === BaseClient) {
                    throw new Error('BaseClient is abstract and should not be directly constructed.');
                }

                if (!config) {
                    throw new Error('Config object required');
                }

                var instanceId = config.instanceId,
                    _config$endpointOverr = config.endpointOverride,
                    endpointOverride = _config$endpointOverr === void 0 ? null : _config$endpointOverr;

                if (instanceId === undefined) {
                    throw new Error('Instance ID is required');
                }

                if (typeof instanceId !== 'string') {
                    throw new Error('Instance ID must be a string');
                }

                if (instanceId.length === 0) {
                    throw new Error('Instance ID cannot be empty');
                }

                if (!('indexedDB' in window)) {
                    throw new Error('Pusher Beams does not support this browser version (IndexedDB not supported)');
                }

                this.instanceId = instanceId;
                this._deviceId = null;
                this._token = null;
                this._userId = null;
                this._deviceStateStore = new DeviceStateStore(instanceId);
                this._endpoint = endpointOverride; // Internal only

                this._platform = platform;
            }

            createClass(BaseClient, [{
                key: "getDeviceId",
                value: function() {
                    var _getDeviceId = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee() {
                            var _this = this;

                            return regenerator.wrap(function _callee$(_context) {
                                while (1) {
                                    switch (_context.prev = _context.next) {
                                        case 0:
                                            _context.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            return _context.abrupt("return", this._ready.then(function() {
                                                return _this._deviceId;
                                            }));

                                        case 3:
                                        case "end":
                                            return _context.stop();
                                    }
                                }
                            }, _callee, this);
                        }));

                    function getDeviceId() {
                        return _getDeviceId.apply(this, arguments);
                    }

                    return getDeviceId;
                }()
            }, {
                key: "getToken",
                value: function() {
                    var _getToken = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee2() {
                            var _this2 = this;

                            return regenerator.wrap(function _callee2$(_context2) {
                                while (1) {
                                    switch (_context2.prev = _context2.next) {
                                        case 0:
                                            _context2.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            return _context2.abrupt("return", this._ready.then(function() {
                                                return _this2._token;
                                            }));

                                        case 3:
                                        case "end":
                                            return _context2.stop();
                                    }
                                }
                            }, _callee2, this);
                        }));

                    function getToken() {
                        return _getToken.apply(this, arguments);
                    }

                    return getToken;
                }()
            }, {
                key: "getUserId",
                value: function() {
                    var _getUserId = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee3() {
                            var _this3 = this;

                            return regenerator.wrap(function _callee3$(_context3) {
                                while (1) {
                                    switch (_context3.prev = _context3.next) {
                                        case 0:
                                            _context3.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            return _context3.abrupt("return", this._ready.then(function() {
                                                return _this3._userId;
                                            }));

                                        case 3:
                                        case "end":
                                            return _context3.stop();
                                    }
                                }
                            }, _callee3, this);
                        }));

                    function getUserId() {
                        return _getUserId.apply(this, arguments);
                    }

                    return getUserId;
                }()
            }, {
                key: "_throwIfNotStarted",
                value: function _throwIfNotStarted(message) {
                    if (!this._deviceId) {
                        throw new Error("".concat(message, ". SDK not registered with Beams. Did you call .start?"));
                    }
                }
            }, {
                key: "_resolveSDKState",
                value: function() {
                    var _resolveSDKState2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee4() {
                            return regenerator.wrap(function _callee4$(_context4) {
                                while (1) {
                                    switch (_context4.prev = _context4.next) {
                                        case 0:
                                            _context4.next = 2;
                                            return this._ready;

                                        case 2:
                                            _context4.next = 4;
                                            return this._detectSubscriptionChange();

                                        case 4:
                                        case "end":
                                            return _context4.stop();
                                    }
                                }
                            }, _callee4, this);
                        }));

                    function _resolveSDKState() {
                        return _resolveSDKState2.apply(this, arguments);
                    }

                    return _resolveSDKState;
                }()
            }, {
                key: "addDeviceInterest",
                value: function() {
                    var _addDeviceInterest = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee5(interest) {
                            var path, options;
                            return regenerator.wrap(function _callee5$(_context5) {
                                while (1) {
                                    switch (_context5.prev = _context5.next) {
                                        case 0:
                                            _context5.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            this._throwIfNotStarted('Could not add Device Interest');

                                            validateInterestName(interest);
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(this._deviceId, "/interests/").concat(encodeURIComponent(interest));
                                            options = {
                                                method: 'POST',
                                                path: path
                                            };
                                            _context5.next = 8;
                                            return doRequest(options);

                                        case 8:
                                        case "end":
                                            return _context5.stop();
                                    }
                                }
                            }, _callee5, this);
                        }));

                    function addDeviceInterest(_x) {
                        return _addDeviceInterest.apply(this, arguments);
                    }

                    return addDeviceInterest;
                }()
            }, {
                key: "removeDeviceInterest",
                value: function() {
                    var _removeDeviceInterest = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee6(interest) {
                            var path, options;
                            return regenerator.wrap(function _callee6$(_context6) {
                                while (1) {
                                    switch (_context6.prev = _context6.next) {
                                        case 0:
                                            _context6.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            this._throwIfNotStarted('Could not remove Device Interest');

                                            validateInterestName(interest);
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(this._deviceId, "/interests/").concat(encodeURIComponent(interest));
                                            options = {
                                                method: 'DELETE',
                                                path: path
                                            };
                                            _context6.next = 8;
                                            return doRequest(options);

                                        case 8:
                                        case "end":
                                            return _context6.stop();
                                    }
                                }
                            }, _callee6, this);
                        }));

                    function removeDeviceInterest(_x2) {
                        return _removeDeviceInterest.apply(this, arguments);
                    }

                    return removeDeviceInterest;
                }()
            }, {
                key: "getDeviceInterests",
                value: function() {
                    var _getDeviceInterests = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee7() {
                            var path, options;
                            return regenerator.wrap(function _callee7$(_context7) {
                                while (1) {
                                    switch (_context7.prev = _context7.next) {
                                        case 0:
                                            _context7.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            this._throwIfNotStarted('Could not get Device Interests');

                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(this._deviceId, "/interests");
                                            options = {
                                                method: 'GET',
                                                path: path
                                            };
                                            _context7.next = 7;
                                            return doRequest(options);

                                        case 7:
                                            _context7.t0 = _context7.sent['interests'];

                                            if (_context7.t0) {
                                                _context7.next = 10;
                                                break;
                                            }

                                            _context7.t0 = [];

                                        case 10:
                                            return _context7.abrupt("return", _context7.t0);

                                        case 11:
                                        case "end":
                                            return _context7.stop();
                                    }
                                }
                            }, _callee7, this);
                        }));

                    function getDeviceInterests() {
                        return _getDeviceInterests.apply(this, arguments);
                    }

                    return getDeviceInterests;
                }()
            }, {
                key: "setDeviceInterests",
                value: function() {
                    var _setDeviceInterests = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee8(interests) {
                            var _iteratorNormalCompletion, _didIteratorError, _iteratorError, _iterator, _step, interest, uniqueInterests, path, options;

                            return regenerator.wrap(function _callee8$(_context8) {
                                while (1) {
                                    switch (_context8.prev = _context8.next) {
                                        case 0:
                                            _context8.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            this._throwIfNotStarted('Could not set Device Interests');

                                            if (!(interests === undefined || interests === null)) {
                                                _context8.next = 5;
                                                break;
                                            }

                                            throw new Error('interests argument is required');

                                        case 5:
                                            if (Array.isArray(interests)) {
                                                _context8.next = 7;
                                                break;
                                            }

                                            throw new Error('interests argument must be an array');

                                        case 7:
                                            if (!(interests.length > MAX_INTERESTS_NUM)) {
                                                _context8.next = 9;
                                                break;
                                            }

                                            throw new Error("Number of interests (".concat(interests.length, ") exceeds maximum of ").concat(MAX_INTERESTS_NUM));

                                        case 9:
                                            _iteratorNormalCompletion = true;
                                            _didIteratorError = false;
                                            _iteratorError = undefined;
                                            _context8.prev = 12;

                                            for (_iterator = interests[Symbol.iterator](); !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                                                interest = _step.value;
                                                validateInterestName(interest);
                                            }

                                            _context8.next = 20;
                                            break;

                                        case 16:
                                            _context8.prev = 16;
                                            _context8.t0 = _context8["catch"](12);
                                            _didIteratorError = true;
                                            _iteratorError = _context8.t0;

                                        case 20:
                                            _context8.prev = 20;
                                            _context8.prev = 21;

                                            if (!_iteratorNormalCompletion && _iterator["return"] != null) {
                                                _iterator["return"]();
                                            }

                                        case 23:
                                            _context8.prev = 23;

                                            if (!_didIteratorError) {
                                                _context8.next = 26;
                                                break;
                                            }

                                            throw _iteratorError;

                                        case 26:
                                            return _context8.finish(23);

                                        case 27:
                                            return _context8.finish(20);

                                        case 28:
                                            uniqueInterests = Array.from(new Set(interests));
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(this._deviceId, "/interests");
                                            options = {
                                                method: 'PUT',
                                                path: path,
                                                body: {
                                                    interests: uniqueInterests
                                                }
                                            };
                                            _context8.next = 33;
                                            return doRequest(options);

                                        case 33:
                                        case "end":
                                            return _context8.stop();
                                    }
                                }
                            }, _callee8, this, [
                                [12, 16, 20, 28],
                                [21, , 23, 27]
                            ]);
                        }));

                    function setDeviceInterests(_x3) {
                        return _setDeviceInterests.apply(this, arguments);
                    }

                    return setDeviceInterests;
                }()
            }, {
                key: "clearDeviceInterests",
                value: function() {
                    var _clearDeviceInterests = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee9() {
                            return regenerator.wrap(function _callee9$(_context9) {
                                while (1) {
                                    switch (_context9.prev = _context9.next) {
                                        case 0:
                                            _context9.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            this._throwIfNotStarted('Could not clear Device Interests');

                                            _context9.next = 5;
                                            return this.setDeviceInterests([]);

                                        case 5:
                                        case "end":
                                            return _context9.stop();
                                    }
                                }
                            }, _callee9, this);
                        }));

                    function clearDeviceInterests() {
                        return _clearDeviceInterests.apply(this, arguments);
                    }

                    return clearDeviceInterests;
                }()
            }, {
                key: "_deleteDevice",
                value: function() {
                        var _deleteDevice2 = asyncToGenerator(
                            /*#__PURE__*/
                            regenerator.mark(function _callee10() {
                                var path, options;
                                return regenerator.wrap(function _callee10$(_context10) {
                                    while (1) {
                                        switch (_context10.prev = _context10.next) {
                                            case 0:
                                                path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(encodeURIComponent(this._deviceId));
                                                options = {
                                                    method: 'DELETE',
                                                    path: path
                                                };
                                                _context10.next = 4;
                                                return doRequest(options);

                                            case 4:
                                            case "end":
                                                return _context10.stop();
                                        }
                                    }
                                }, _callee10, this);
                            }));

                        function _deleteDevice() {
                            return _deleteDevice2.apply(this, arguments);
                        }

                        return _deleteDevice;
                    }() // TODO is this ever used?

                /**
                 * Submit SDK version and browser details (via the user agent) to Pusher Beams.
                 */

            }, {
                key: "_updateDeviceMetadata",
                value: function() {
                    var _updateDeviceMetadata2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee11() {
                            var userAgent, storedUserAgent, storedSdkVersion, path, metadata, options;
                            return regenerator.wrap(function _callee11$(_context11) {
                                while (1) {
                                    switch (_context11.prev = _context11.next) {
                                        case 0:
                                            userAgent = window.navigator.userAgent;
                                            _context11.next = 3;
                                            return this._deviceStateStore.getLastSeenUserAgent();

                                        case 3:
                                            storedUserAgent = _context11.sent;
                                            _context11.next = 6;
                                            return this._deviceStateStore.getLastSeenSdkVersion();

                                        case 6:
                                            storedSdkVersion = _context11.sent;

                                            if (!(userAgent === storedUserAgent && version === storedSdkVersion)) {
                                                _context11.next = 9;
                                                break;
                                            }

                                            return _context11.abrupt("return");

                                        case 9:
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(this._deviceId, "/metadata");
                                            metadata = {
                                                sdkVersion: version
                                            };
                                            options = {
                                                method: 'PUT',
                                                path: path,
                                                body: metadata
                                            };
                                            _context11.next = 14;
                                            return doRequest(options);

                                        case 14:
                                            _context11.next = 16;
                                            return this._deviceStateStore.setLastSeenSdkVersion(version);

                                        case 16:
                                            _context11.next = 18;
                                            return this._deviceStateStore.setLastSeenUserAgent(userAgent);

                                        case 18:
                                        case "end":
                                            return _context11.stop();
                                    }
                                }
                            }, _callee11, this);
                        }));

                    function _updateDeviceMetadata() {
                        return _updateDeviceMetadata2.apply(this, arguments);
                    }

                    return _updateDeviceMetadata;
                }()
            }, {
                key: "_registerDevice",
                value: function() {
                    var _registerDevice2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee12(device) {
                            var path, options, response;
                            return regenerator.wrap(function _callee12$(_context12) {
                                while (1) {
                                    switch (_context12.prev = _context12.next) {
                                        case 0:
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform);
                                            options = {
                                                method: 'POST',
                                                path: path,
                                                body: device
                                            };
                                            _context12.next = 4;
                                            return doRequest(options);

                                        case 4:
                                            response = _context12.sent;
                                            return _context12.abrupt("return", response.id);

                                        case 6:
                                        case "end":
                                            return _context12.stop();
                                    }
                                }
                            }, _callee12, this);
                        }));

                    function _registerDevice(_x4) {
                        return _registerDevice2.apply(this, arguments);
                    }

                    return _registerDevice;
                }()
            }, {
                key: "setUserId",
                value: function() {
                    var _setUserId = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee13(userId, tokenProvider) {
                            var error, path, _ref, beamsAuthToken, options;

                            return regenerator.wrap(function _callee13$(_context13) {
                                while (1) {
                                    switch (_context13.prev = _context13.next) {
                                        case 0:
                                            _context13.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            if (this._isSupportedBrowser()) {
                                                _context13.next = 4;
                                                break;
                                            }

                                            return _context13.abrupt("return");

                                        case 4:
                                            if (!(this._deviceId === null)) {
                                                _context13.next = 7;
                                                break;
                                            }

                                            error = new Error('.start must be called before .setUserId');
                                            return _context13.abrupt("return", Promise.reject(error));

                                        case 7:
                                            if (!(typeof userId !== 'string')) {
                                                _context13.next = 9;
                                                break;
                                            }

                                            throw new Error("User ID must be a string (was ".concat(userId, ")"));

                                        case 9:
                                            if (!(userId === '')) {
                                                _context13.next = 11;
                                                break;
                                            }

                                            throw new Error('User ID cannot be the empty string');

                                        case 11:
                                            if (!(this._userId !== null && this._userId !== userId)) {
                                                _context13.next = 13;
                                                break;
                                            }

                                            throw new Error('Changing the `userId` is not allowed.');

                                        case 13:
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/devices/").concat(this._platform, "/").concat(this._deviceId, "/user");
                                            _context13.next = 16;
                                            return tokenProvider.fetchToken(userId);

                                        case 16:
                                            _ref = _context13.sent;
                                            beamsAuthToken = _ref.token;
                                            options = {
                                                method: 'PUT',
                                                path: path,
                                                headers: {
                                                    Authorization: "Bearer ".concat(beamsAuthToken)
                                                }
                                            };
                                            _context13.next = 21;
                                            return doRequest(options);

                                        case 21:
                                            this._userId = userId;
                                            return _context13.abrupt("return", this._deviceStateStore.setUserId(userId));

                                        case 23:
                                        case "end":
                                            return _context13.stop();
                                    }
                                }
                            }, _callee13, this);
                        }));

                    function setUserId(_x5, _x6) {
                        return _setUserId.apply(this, arguments);
                    }

                    return setUserId;
                }()
            }, {
                key: "start",
                value: function() {
                    var _start = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee14() {
                            return regenerator.wrap(function _callee14$(_context14) {
                                while (1) {
                                    switch (_context14.prev = _context14.next) {
                                        case 0:
                                            throwNotImplementedError('start');

                                        case 1:
                                        case "end":
                                            return _context14.stop();
                                    }
                                }
                            }, _callee14);
                        }));

                    function start() {
                        return _start.apply(this, arguments);
                    }

                    return start;
                }()
            }, {
                key: "getRegistrationState",
                value: function() {
                    var _getRegistrationState = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee15() {
                            return regenerator.wrap(function _callee15$(_context15) {
                                while (1) {
                                    switch (_context15.prev = _context15.next) {
                                        case 0:
                                            throwNotImplementedError('getRegistrationState');

                                        case 1:
                                        case "end":
                                            return _context15.stop();
                                    }
                                }
                            }, _callee15);
                        }));

                    function getRegistrationState() {
                        return _getRegistrationState.apply(this, arguments);
                    }

                    return getRegistrationState;
                }()
            }, {
                key: "stop",
                value: function() {
                    var _stop = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee16() {
                            return regenerator.wrap(function _callee16$(_context16) {
                                while (1) {
                                    switch (_context16.prev = _context16.next) {
                                        case 0:
                                            throwNotImplementedError('stop');

                                        case 1:
                                        case "end":
                                            return _context16.stop();
                                    }
                                }
                            }, _callee16);
                        }));

                    function stop() {
                        return _stop.apply(this, arguments);
                    }

                    return stop;
                }()
            }, {
                key: "clearAllState",
                value: function() {
                    var _clearAllState = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee17() {
                            return regenerator.wrap(function _callee17$(_context17) {
                                while (1) {
                                    switch (_context17.prev = _context17.next) {
                                        case 0:
                                            throwNotImplementedError('clearAllState');

                                        case 1:
                                        case "end":
                                            return _context17.stop();
                                    }
                                }
                            }, _callee17);
                        }));

                    function clearAllState() {
                        return _clearAllState.apply(this, arguments);
                    }

                    return clearAllState;
                }()
            }, {
                key: "_baseURL",
                get: function get() {
                    if (this._endpoint !== null) {
                        return this._endpoint;
                    }

                    return "https://".concat(this.instanceId, ".pushnotifications.pusher.com");
                }
            }]);

            return BaseClient;
        }();

    function throwNotImplementedError(method) {
        throw new Error("".concat(method, " not implemented on abstract BaseClient.") + 'Instantiate either WebPushClient or SafariClient');
    }

    function validateInterestName(interest) {
        if (interest === undefined || interest === null) {
            throw new Error('Interest name is required');
        }

        if (typeof interest !== 'string') {
            throw new Error("Interest ".concat(interest, " is not a string"));
        }

        if (!INTERESTS_REGEX.test(interest)) {
            throw new Error("interest \"".concat(interest, "\" contains a forbidden character. ") + 'Allowed characters are: ASCII upper/lower-case letters, ' + 'numbers or one of _-=@,.;');
        }

        if (interest.length > MAX_INTEREST_LENGTH) {
            throw new Error("Interest is longer than the maximum of ".concat(MAX_INTEREST_LENGTH, " chars"));
        }
    }

    var SERVICE_WORKER_URL = "/tgv/service-worker.js?pusherBeamsWebSDKVersion=".concat(version);
    var platform = 'web';
    var WebPushClient =
        /*#__PURE__*/
        function(_BaseClient) {
            inherits(WebPushClient, _BaseClient);

            function WebPushClient(config) {
                var _this;

                classCallCheck(this, WebPushClient);

                _this = possibleConstructorReturn(this, getPrototypeOf(WebPushClient).call(this, config, platform));

                if (!window.isSecureContext) {
                    throw new Error('Pusher Beams relies on Service Workers, which only work in secure contexts. Check that your page is being served from localhost/over HTTPS');
                }

                if (!('serviceWorker' in navigator)) {
                    throw new Error('Pusher Beams does not support this browser version (Service Workers not supported)');
                }

                if (!('PushManager' in window)) {
                    throw new Error('Pusher Beams does not support this browser version (Web Push not supported)');
                }

                var _config$serviceWorker = config.serviceWorkerRegistration,
                    serviceWorkerRegistration = _config$serviceWorker === void 0 ? null : _config$serviceWorker;

                if (serviceWorkerRegistration) {
                    var serviceWorkerScope = serviceWorkerRegistration.scope;
                    var currentURL = window.location.href;
                    var scopeMatchesCurrentPage = currentURL.startsWith(serviceWorkerScope);

                    if (!scopeMatchesCurrentPage) {
                        throw new Error("Could not initialize Pusher web push: current page not in serviceWorkerRegistration scope (".concat(serviceWorkerScope, ")"));
                    }
                }

                _this._serviceWorkerRegistration = serviceWorkerRegistration;
                _this._ready = _this._init();
                return _this;
            }

            createClass(WebPushClient, [{
                key: "_init",
                value: function() {
                    var _init2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee() {
                            return regenerator.wrap(function _callee$(_context) {
                                while (1) {
                                    switch (_context.prev = _context.next) {
                                        case 0:
                                            if (!(this._deviceId !== null)) {
                                                _context.next = 2;
                                                break;
                                            }

                                            return _context.abrupt("return");

                                        case 2:
                                            _context.next = 4;
                                            return this._deviceStateStore.connect();

                                        case 4:
                                            if (!this._serviceWorkerRegistration) {
                                                _context.next = 9;
                                                break;
                                            }

                                            _context.next = 7;
                                            return window.navigator.serviceWorker.ready;

                                        case 7:
                                            _context.next = 12;
                                            break;

                                        case 9:
                                            _context.next = 11;
                                            return getServiceWorkerRegistration();

                                        case 11:
                                            this._serviceWorkerRegistration = _context.sent;

                                        case 12:
                                            _context.next = 14;
                                            return this._detectSubscriptionChange();

                                        case 14:
                                            _context.next = 16;
                                            return this._deviceStateStore.getDeviceId();

                                        case 16:
                                            this._deviceId = _context.sent;
                                            _context.next = 19;
                                            return this._deviceStateStore.getToken();

                                        case 19:
                                            this._token = _context.sent;
                                            _context.next = 22;
                                            return this._deviceStateStore.getUserId();

                                        case 22:
                                            this._userId = _context.sent;

                                        case 23:
                                        case "end":
                                            return _context.stop();
                                    }
                                }
                            }, _callee, this);
                        }));

                    function _init() {
                        return _init2.apply(this, arguments);
                    }

                    return _init;
                }()
            }, {
                key: "_detectSubscriptionChange",
                value: function() {
                    var _detectSubscriptionChange2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee2() {
                            var storedToken, actualToken, pushTokenHasChanged;
                            return regenerator.wrap(function _callee2$(_context2) {
                                while (1) {
                                    switch (_context2.prev = _context2.next) {
                                        case 0:
                                            _context2.next = 2;
                                            return this._deviceStateStore.getToken();

                                        case 2:
                                            storedToken = _context2.sent;
                                            _context2.next = 5;
                                            return getWebPushToken(this._serviceWorkerRegistration);

                                        case 5:
                                            actualToken = _context2.sent;
                                            pushTokenHasChanged = storedToken !== actualToken;

                                            if (!pushTokenHasChanged) {
                                                _context2.next = 13;
                                                break;
                                            }

                                            _context2.next = 10;
                                            return this._deviceStateStore.clear();

                                        case 10:
                                            this._deviceId = null;
                                            this._token = null;
                                            this._userId = null;

                                        case 13:
                                        case "end":
                                            return _context2.stop();
                                    }
                                }
                            }, _callee2, this);
                        }));

                    function _detectSubscriptionChange() {
                        return _detectSubscriptionChange2.apply(this, arguments);
                    }

                    return _detectSubscriptionChange;
                }()
            }, {
                key: "start",
                value: function() {
                    var _start = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee3() {
                            var _ref, publicKey, token, deviceId;

                            return regenerator.wrap(function _callee3$(_context3) {
                                while (1) {
                                    switch (_context3.prev = _context3.next) {
                                        case 0:
                                            _context3.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            if (this._isSupportedBrowser()) {
                                                _context3.next = 4;
                                                break;
                                            }

                                            return _context3.abrupt("return", this);

                                        case 4:
                                            if (!(this._deviceId !== null)) {
                                                _context3.next = 6;
                                                break;
                                            }

                                            return _context3.abrupt("return", this);

                                        case 6:
                                            _context3.next = 8;
                                            return this._getPublicKey();

                                        case 8:
                                            _ref = _context3.sent;
                                            publicKey = _ref.vapidPublicKey;
                                            _context3.next = 12;
                                            return this._getPushToken(publicKey);

                                        case 12:
                                            token = _context3.sent;
                                            _context3.next = 15;
                                            return this._registerDevice(token);

                                        case 15:
                                            deviceId = _context3.sent;
                                            _context3.next = 18;
                                            return this._deviceStateStore.setToken(token);

                                        case 18:
                                            _context3.next = 20;
                                            return this._deviceStateStore.setDeviceId(deviceId);

                                        case 20:
                                            _context3.next = 22;
                                            return this._deviceStateStore.setLastSeenSdkVersion(version);

                                        case 22:
                                            _context3.next = 24;
                                            return this._deviceStateStore.setLastSeenUserAgent(window.navigator.userAgent);

                                        case 24:
                                            this._token = token;
                                            this._deviceId = deviceId;
                                            return _context3.abrupt("return", this);

                                        case 27:
                                        case "end":
                                            return _context3.stop();
                                    }
                                }
                            }, _callee3, this);
                        }));

                    function start() {
                        return _start.apply(this, arguments);
                    }

                    return start;
                }()
            }, {
                key: "getRegistrationState",
                value: function() {
                    var _getRegistrationState = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee4() {
                            return regenerator.wrap(function _callee4$(_context4) {
                                while (1) {
                                    switch (_context4.prev = _context4.next) {
                                        case 0:
                                            _context4.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            if (!(Notification.permission === 'denied')) {
                                                _context4.next = 4;
                                                break;
                                            }

                                            return _context4.abrupt("return", RegistrationState.PERMISSION_DENIED);

                                        case 4:
                                            if (!(Notification.permission === 'granted' && this._deviceId !== null)) {
                                                _context4.next = 6;
                                                break;
                                            }

                                            return _context4.abrupt("return", RegistrationState.PERMISSION_GRANTED_REGISTERED_WITH_BEAMS);

                                        case 6:
                                            if (!(Notification.permission === 'granted' && this._deviceId === null)) {
                                                _context4.next = 8;
                                                break;
                                            }

                                            return _context4.abrupt("return", RegistrationState.PERMISSION_GRANTED_NOT_REGISTERED_WITH_BEAMS);

                                        case 8:
                                            return _context4.abrupt("return", RegistrationState.PERMISSION_PROMPT_REQUIRED);

                                        case 9:
                                        case "end":
                                            return _context4.stop();
                                    }
                                }
                            }, _callee4, this);
                        }));

                    function getRegistrationState() {
                        return _getRegistrationState.apply(this, arguments);
                    }

                    return getRegistrationState;
                }()
            }, {
                key: "stop",
                value: function() {
                    var _stop = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee5() {
                            return regenerator.wrap(function _callee5$(_context5) {
                                while (1) {
                                    switch (_context5.prev = _context5.next) {
                                        case 0:
                                            _context5.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            if (this._isSupportedBrowser()) {
                                                _context5.next = 4;
                                                break;
                                            }

                                            return _context5.abrupt("return");

                                        case 4:
                                            if (!(this._deviceId === null)) {
                                                _context5.next = 6;
                                                break;
                                            }

                                            return _context5.abrupt("return");

                                        case 6:
                                            _context5.next = 8;
                                            return this._deleteDevice();

                                        case 8:
                                            _context5.next = 10;
                                            return this._deviceStateStore.clear();

                                        case 10:
                                            this._clearPushToken()["catch"](function() {}); // Not awaiting this, best effort.


                                            this._deviceId = null;
                                            this._token = null;
                                            this._userId = null;

                                        case 14:
                                        case "end":
                                            return _context5.stop();
                                    }
                                }
                            }, _callee5, this);
                        }));

                    function stop() {
                        return _stop.apply(this, arguments);
                    }

                    return stop;
                }()
            }, {
                key: "clearAllState",
                value: function() {
                    var _clearAllState = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee6() {
                            return regenerator.wrap(function _callee6$(_context6) {
                                while (1) {
                                    switch (_context6.prev = _context6.next) {
                                        case 0:
                                            if (this._isSupportedBrowser()) {
                                                _context6.next = 2;
                                                break;
                                            }

                                            return _context6.abrupt("return");

                                        case 2:
                                            _context6.next = 4;
                                            return this.stop();

                                        case 4:
                                            _context6.next = 6;
                                            return this.start();

                                        case 6:
                                        case "end":
                                            return _context6.stop();
                                    }
                                }
                            }, _callee6, this);
                        }));

                    function clearAllState() {
                        return _clearAllState.apply(this, arguments);
                    }

                    return clearAllState;
                }()
            }, {
                key: "_getPublicKey",
                value: function() {
                    var _getPublicKey2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee7() {
                            var path, options;
                            return regenerator.wrap(function _callee7$(_context7) {
                                while (1) {
                                    switch (_context7.prev = _context7.next) {
                                        case 0:
                                            path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/web-vapid-public-key");
                                            options = {
                                                method: 'GET',
                                                path: path
                                            };
                                            return _context7.abrupt("return", doRequest(options));

                                        case 3:
                                        case "end":
                                            return _context7.stop();
                                    }
                                }
                            }, _callee7, this);
                        }));

                    function _getPublicKey() {
                        return _getPublicKey2.apply(this, arguments);
                    }

                    return _getPublicKey;
                }()
            }, {
                key: "_getPushToken",
                value: function() {
                    var _getPushToken2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee8(publicKey) {
                            var sub;
                            return regenerator.wrap(function _callee8$(_context8) {
                                while (1) {
                                    switch (_context8.prev = _context8.next) {
                                        case 0:
                                            _context8.prev = 0;
                                            _context8.next = 3;
                                            return this._clearPushToken();

                                        case 3:
                                            _context8.next = 5;
                                            return this._serviceWorkerRegistration.pushManager.subscribe({
                                                userVisibleOnly: true,
                                                applicationServerKey: urlBase64ToUInt8Array(publicKey)
                                            });

                                        case 5:
                                            sub = _context8.sent;
                                            return _context8.abrupt("return", btoa(JSON.stringify(sub)));

                                        case 9:
                                            _context8.prev = 9;
                                            _context8.t0 = _context8["catch"](0);
                                            return _context8.abrupt("return", Promise.reject(_context8.t0));

                                        case 12:
                                        case "end":
                                            return _context8.stop();
                                    }
                                }
                            }, _callee8, this, [
                                [0, 9]
                            ]);
                        }));

                    function _getPushToken(_x) {
                        return _getPushToken2.apply(this, arguments);
                    }

                    return _getPushToken;
                }()
            }, {
                key: "_clearPushToken",
                value: function() {
                    var _clearPushToken2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee9() {
                            return regenerator.wrap(function _callee9$(_context9) {
                                while (1) {
                                    switch (_context9.prev = _context9.next) {
                                        case 0:
                                            return _context9.abrupt("return", navigator.serviceWorker.ready.then(function(reg) {
                                                return reg.pushManager.getSubscription();
                                            }).then(function(sub) {
                                                if (sub) sub.unsubscribe();
                                            }));

                                        case 1:
                                        case "end":
                                            return _context9.stop();
                                    }
                                }
                            }, _callee9);
                        }));

                    function _clearPushToken() {
                        return _clearPushToken2.apply(this, arguments);
                    }

                    return _clearPushToken;
                }()
            }, {
                key: "_registerDevice",
                value: function() {
                        var _registerDevice2 = asyncToGenerator(
                            /*#__PURE__*/
                            regenerator.mark(function _callee10(token) {
                                return regenerator.wrap(function _callee10$(_context10) {
                                    while (1) {
                                        switch (_context10.prev = _context10.next) {
                                            case 0:
                                                _context10.next = 2;
                                                return get(getPrototypeOf(WebPushClient.prototype), "_registerDevice", this).call(this, {
                                                    token: token,
                                                    metadata: {
                                                        sdkVersion: version
                                                    }
                                                });

                                            case 2:
                                                return _context10.abrupt("return", _context10.sent);

                                            case 3:
                                            case "end":
                                                return _context10.stop();
                                        }
                                    }
                                }, _callee10, this);
                            }));

                        function _registerDevice(_x2) {
                            return _registerDevice2.apply(this, arguments);
                        }

                        return _registerDevice;
                    }()
                    /**
                     * Modified from https://stackoverflow.com/questions/4565112
                     */

            }, {
                key: "_isSupportedBrowser",
                value: function _isSupportedBrowser() {
                    var winNav = window.navigator;
                    var vendorName = winNav.vendor;
                    var isChromium = window.chrome !== null && typeof window.chrome !== 'undefined';
                    var isOpera = winNav.userAgent.indexOf('OPR') > -1;
                    var isEdge = winNav.userAgent.indexOf('Edg') > -1;
                    var isFirefox = winNav.userAgent.indexOf('Firefox') > -1;
                    var isChrome = isChromium && vendorName === 'Google Inc.' && !isEdge && !isOpera;
                    var isSupported = isChrome || isOpera || isFirefox || isEdge;

                    if (!isSupported) {
                        console.warn('Pusher Web Push Notifications supports Chrome, Firefox, Edge and Opera.');
                    }

                    return isSupported;
                }
            }]);

            return WebPushClient;
        }(BaseClient);

    function getServiceWorkerRegistration() {
        return _getServiceWorkerRegistration.apply(this, arguments);
    }

    function _getServiceWorkerRegistration() {
        _getServiceWorkerRegistration = asyncToGenerator(
            /*#__PURE__*/
            regenerator.mark(function _callee11() {
                var _ref2, swStatusCode;

                return regenerator.wrap(function _callee11$(_context11) {
                    while (1) {
                        switch (_context11.prev = _context11.next) {
                            case 0:
                                _context11.next = 2;
                                return fetch(SERVICE_WORKER_URL);

                            case 2:
                                _ref2 = _context11.sent;
                                swStatusCode = _ref2.status;

                                if (!(swStatusCode !== 200)) {
                                    _context11.next = 6;
                                    break;
                                }

                                throw new Error('Cannot start SDK, service worker missing: No file found at /service-worker.js');

                            case 6:
                                window.navigator.serviceWorker.register(SERVICE_WORKER_URL, {
                                    // explicitly opting out of `importScripts` caching just in case our
                                    // customers decides to host and serve the imported scripts and
                                    // accidentally set `Cache-Control` to something other than `max-age=0`
                                    updateViaCache: 'none'
                                });
                                return _context11.abrupt("return", window.navigator.serviceWorker.ready);

                            case 8:
                            case "end":
                                return _context11.stop();
                        }
                    }
                }, _callee11);
            }));
        return _getServiceWorkerRegistration.apply(this, arguments);
    }

    function getWebPushToken(swReg) {
        return swReg.pushManager.getSubscription().then(function(sub) {
            return !sub ? null : encodeSubscription(sub);
        });
    }

    function encodeSubscription(sub) {
        return btoa(JSON.stringify(sub));
    }

    function urlBase64ToUInt8Array(base64String) {
        var padding = '='.repeat((4 - base64String.length % 4) % 4);
        var base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        var rawData = window.atob(base64);
        return Uint8Array.from(toConsumableArray(rawData).map(function(_char) {
            return _char.charCodeAt(0);
        }));
    }

    var platform$1 = 'safari';
    var SafariClient =
        /*#__PURE__*/
        function(_BaseClient) {
            inherits(SafariClient, _BaseClient);

            function SafariClient(config) {
                var _this;

                classCallCheck(this, SafariClient);

                _this = possibleConstructorReturn(this, getPrototypeOf(SafariClient).call(this, config, platform$1));

                if (!_this._isSupportedBrowser()) {
                    throw new Error('Pusher Beams does not support this Safari version (Safari Push Notifications not supported)');
                }

                _this._ready = _this._init();
                return _this;
            }

            createClass(SafariClient, [{
                key: "_init",
                value: function() {
                    var _init2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee() {
                            var _ref, websitePushId;

                            return regenerator.wrap(function _callee$(_context) {
                                while (1) {
                                    switch (_context.prev = _context.next) {
                                        case 0:
                                            _context.next = 2;
                                            return this._fetchWebsitePushId();

                                        case 2:
                                            _ref = _context.sent;
                                            websitePushId = _ref.websitePushId;
                                            this._websitePushId = websitePushId;
                                            this._serviceUrl = "".concat(this._baseURL, "/safari_api/v1/instances/").concat(encodeURIComponent(this.instanceId));

                                            if (!(this._deviceId !== null)) {
                                                _context.next = 8;
                                                break;
                                            }

                                            return _context.abrupt("return");

                                        case 8:
                                            _context.next = 10;
                                            return this._deviceStateStore.connect();

                                        case 10:
                                            _context.next = 12;
                                            return this._detectSubscriptionChange();

                                        case 12:
                                            _context.next = 14;
                                            return this._deviceStateStore.getDeviceId(this._websitePushId);

                                        case 14:
                                            this._deviceId = _context.sent;
                                            _context.next = 17;
                                            return this._deviceStateStore.getToken();

                                        case 17:
                                            this._token = _context.sent;
                                            _context.next = 20;
                                            return this._deviceStateStore.getUserId();

                                        case 20:
                                            this._userId = _context.sent;

                                        case 21:
                                        case "end":
                                            return _context.stop();
                                    }
                                }
                            }, _callee, this);
                        }));

                    function _init() {
                        return _init2.apply(this, arguments);
                    }

                    return _init;
                }()
            }, {
                key: "_detectSubscriptionChange",
                value: function() {
                    var _detectSubscriptionChange2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee2() {
                            var storedToken, _getCurrentPermission, actualToken, tokenHasChanged;

                            return regenerator.wrap(function _callee2$(_context2) {
                                while (1) {
                                    switch (_context2.prev = _context2.next) {
                                        case 0:
                                            _context2.next = 2;
                                            return this._deviceStateStore.getToken();

                                        case 2:
                                            storedToken = _context2.sent;
                                            _getCurrentPermission = getCurrentPermission(this._websitePushId), actualToken = _getCurrentPermission.deviceToken;
                                            tokenHasChanged = storedToken !== actualToken;

                                            if (!tokenHasChanged) {
                                                _context2.next = 11;
                                                break;
                                            }

                                            _context2.next = 8;
                                            return this._deviceStateStore.clear();

                                        case 8:
                                            this._deviceId = null;
                                            this._token = null;
                                            this._userId = null;

                                        case 11:
                                        case "end":
                                            return _context2.stop();
                                    }
                                }
                            }, _callee2, this);
                        }));

                    function _detectSubscriptionChange() {
                        return _detectSubscriptionChange2.apply(this, arguments);
                    }

                    return _detectSubscriptionChange;
                }()
            }, {
                key: "_requestPermission",
                value: function _requestPermission() {
                    var _this2 = this;

                    // Check to see whether we've already asked for permission, if we have we
                    // can't ask again
                    var _getCurrentPermission2 = getCurrentPermission(this._websitePushId),
                        deviceToken = _getCurrentPermission2.deviceToken,
                        permission = _getCurrentPermission2.permission;

                    if (permission !== 'default') {
                        return Promise.resolve({
                            deviceToken: deviceToken,
                            permission: permission
                        });
                    }

                    return new Promise(function(resolve, reject) {
                        try {
                            window.safari.pushNotification.requestPermission(_this2._serviceUrl, _this2._websitePushId, {}, resolve);
                        } catch (e) {
                            reject(e);
                        }
                    });
                }
            }, {
                key: "start",
                value: function() {
                    var _start = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee3() {
                            var _ref2, deviceToken, permission, deviceId;

                            return regenerator.wrap(function _callee3$(_context3) {
                                while (1) {
                                    switch (_context3.prev = _context3.next) {
                                        case 0:
                                            _context3.next = 2;
                                            return this._ready;

                                        case 2:
                                            if (!(this._deviceId !== null)) {
                                                _context3.next = 4;
                                                break;
                                            }

                                            return _context3.abrupt("return", this);

                                        case 4:
                                            _context3.next = 6;
                                            return this._requestPermission();

                                        case 6:
                                            _ref2 = _context3.sent;
                                            deviceToken = _ref2.deviceToken;
                                            permission = _ref2.permission;

                                            if (!(permission == 'granted')) {
                                                _context3.next = 25;
                                                break;
                                            }

                                            _context3.next = 12;
                                            return this._registerDevice(deviceToken, this._websitePushId);

                                        case 12:
                                            deviceId = _context3.sent;
                                            _context3.next = 15;
                                            return this._deviceStateStore.setToken(deviceToken);

                                        case 15:
                                            _context3.next = 17;
                                            return this._deviceStateStore.setDeviceId(deviceId);

                                        case 17:
                                            _context3.next = 19;
                                            return this._deviceStateStore.setLastSeenSdkVersion(version);

                                        case 19:
                                            _context3.next = 21;
                                            return this._deviceStateStore.setLastSeenUserAgent(window.navigator.userAgent);

                                        case 21:
                                            this._token = deviceToken;
                                            this._deviceId = deviceId;
                                            _context3.next = 27;
                                            break;

                                        case 25:
                                            if (!(permission === 'denied')) {
                                                _context3.next = 27;
                                                break;
                                            }

                                            throw new Error('Registration failed - permission denied');

                                        case 27:
                                            return _context3.abrupt("return", this);

                                        case 28:
                                        case "end":
                                            return _context3.stop();
                                    }
                                }
                            }, _callee3, this);
                        }));

                    function start() {
                        return _start.apply(this, arguments);
                    }

                    return start;
                }()
            }, {
                key: "getRegistrationState",
                value: function() {
                    var _getRegistrationState = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee4() {
                            var _getCurrentPermission3, permission;

                            return regenerator.wrap(function _callee4$(_context4) {
                                while (1) {
                                    switch (_context4.prev = _context4.next) {
                                        case 0:
                                            _context4.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            _getCurrentPermission3 = getCurrentPermission(this._websitePushId), permission = _getCurrentPermission3.permission;

                                            if (!(permission === 'denied')) {
                                                _context4.next = 5;
                                                break;
                                            }

                                            return _context4.abrupt("return", RegistrationState.PERMISSION_DENIED);

                                        case 5:
                                            if (!(permission === 'granted' && this._deviceId !== null)) {
                                                _context4.next = 7;
                                                break;
                                            }

                                            return _context4.abrupt("return", RegistrationState.PERMISSION_GRANTED_REGISTERED_WITH_BEAMS);

                                        case 7:
                                            if (!(permission === 'granted' && this._deviceId === null)) {
                                                _context4.next = 9;
                                                break;
                                            }

                                            return _context4.abrupt("return", RegistrationState.PERMISSION_GRANTED_NOT_REGISTERED_WITH_BEAMS);

                                        case 9:
                                            return _context4.abrupt("return", RegistrationState.PERMISSION_PROMPT_REQUIRED);

                                        case 10:
                                        case "end":
                                            return _context4.stop();
                                    }
                                }
                            }, _callee4, this);
                        }));

                    function getRegistrationState() {
                        return _getRegistrationState.apply(this, arguments);
                    }

                    return getRegistrationState;
                }()
            }, {
                key: "clearAllState",
                value: function() {
                    var _clearAllState = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee5() {
                            return regenerator.wrap(function _callee5$(_context5) {
                                while (1) {
                                    switch (_context5.prev = _context5.next) {
                                        case 0:
                                            if (this._isSupportedBrowser()) {
                                                _context5.next = 2;
                                                break;
                                            }

                                            return _context5.abrupt("return");

                                        case 2:
                                            _context5.next = 4;
                                            return this._deleteDevice();

                                        case 4:
                                            _context5.next = 6;
                                            return this._deviceStateStore.clear();

                                        case 6:
                                            this._deviceId = null;
                                            this._token = null;
                                            this._userId = null;

                                        case 9:
                                        case "end":
                                            return _context5.stop();
                                    }
                                }
                            }, _callee5, this);
                        }));

                    function clearAllState() {
                        return _clearAllState.apply(this, arguments);
                    }

                    return clearAllState;
                }()
            }, {
                key: "stop",
                value: function() {
                    var _stop = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee6() {
                            return regenerator.wrap(function _callee6$(_context6) {
                                while (1) {
                                    switch (_context6.prev = _context6.next) {
                                        case 0:
                                            _context6.next = 2;
                                            return this._resolveSDKState();

                                        case 2:
                                            if (this._isSupportedBrowser()) {
                                                _context6.next = 4;
                                                break;
                                            }

                                            return _context6.abrupt("return");

                                        case 4:
                                            if (!(this._deviceId === null)) {
                                                _context6.next = 6;
                                                break;
                                            }

                                            return _context6.abrupt("return");

                                        case 6:
                                            _context6.next = 8;
                                            return this.clearAllState();

                                        case 8:
                                        case "end":
                                            return _context6.stop();
                                    }
                                }
                            }, _callee6, this);
                        }));

                    function stop() {
                        return _stop.apply(this, arguments);
                    }

                    return stop;
                }()
            }, {
                key: "_registerDevice",
                value: function() {
                    var _registerDevice2 = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee7(token, websitePushId) {
                            return regenerator.wrap(function _callee7$(_context7) {
                                while (1) {
                                    switch (_context7.prev = _context7.next) {
                                        case 0:
                                            _context7.next = 2;
                                            return get(getPrototypeOf(SafariClient.prototype), "_registerDevice", this).call(this, {
                                                token: token,
                                                websitePushId: websitePushId,
                                                metadata: {
                                                    sdkVersion: version
                                                }
                                            });

                                        case 2:
                                            return _context7.abrupt("return", _context7.sent);

                                        case 3:
                                        case "end":
                                            return _context7.stop();
                                    }
                                }
                            }, _callee7, this);
                        }));

                    function _registerDevice(_x, _x2) {
                        return _registerDevice2.apply(this, arguments);
                    }

                    return _registerDevice;
                }()
            }, {
                key: "_fetchWebsitePushId",
                value: function _fetchWebsitePushId() {
                    var path = "".concat(this._baseURL, "/device_api/v1/instances/").concat(encodeURIComponent(this.instanceId), "/safari-website-push-id");
                    var options = {
                        method: 'GET',
                        path: path
                    };
                    return doRequest(options);
                }
            }, {
                key: "_isSupportedBrowser",
                value: function _isSupportedBrowser() {
                    return 'safari' in window && 'pushNotification' in window.safari;
                }
            }]);

            return SafariClient;
        }(BaseClient);

    function getCurrentPermission(websitePushId) {
        return window.safari.pushNotification.permission(websitePushId);
    }

    var TokenProvider =
        /*#__PURE__*/
        function() {
            function TokenProvider() {
                var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
                    url = _ref.url,
                    queryParams = _ref.queryParams,
                    headers = _ref.headers,
                    credentials = _ref.credentials;

                classCallCheck(this, TokenProvider);

                this.url = url;
                this.queryParams = queryParams;
                this.headers = headers;
                this.credentials = credentials;
            }

            createClass(TokenProvider, [{
                key: "fetchToken",
                value: function() {
                    var _fetchToken = asyncToGenerator(
                        /*#__PURE__*/
                        regenerator.mark(function _callee(userId) {
                            var queryParams, encodedParams, options, response;
                            return regenerator.wrap(function _callee$(_context) {
                                while (1) {
                                    switch (_context.prev = _context.next) {
                                        case 0:
                                            queryParams = objectSpread({
                                                user_id: userId
                                            }, this.queryParams);
                                            encodedParams = Object.entries(queryParams).map(function(kv) {
                                                return kv.map(encodeURIComponent).join('=');
                                            }).join('&');
                                            options = {
                                                method: 'GET',
                                                path: "".concat(this.url, "?").concat(encodedParams),
                                                headers: this.headers,
                                                credentials: this.credentials
                                            };
                                            _context.next = 5;
                                            return doRequest(options);

                                        case 5:
                                            response = _context.sent;
                                            return _context.abrupt("return", response);

                                        case 7:
                                        case "end":
                                            return _context.stop();
                                    }
                                }
                            }, _callee, this);
                        }));

                    function fetchToken(_x) {
                        return _fetchToken.apply(this, arguments);
                    }

                    return fetchToken;
                }()
            }]);

            return TokenProvider;
        }();

    function Client(config) {
        if ('safari' in window) {
            return new SafariClient(config);
        }

        return new WebPushClient(config);
    }

    exports.Client = Client;
    exports.RegistrationState = RegistrationState;
    exports.TokenProvider = TokenProvider;

    return exports;

}({}));