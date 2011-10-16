/**
 * phpRack: Integration Testing Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available
 * through the world-wide-web at this URL: http://www.phprack.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@phprack.com so we can send you a copy immediately.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @copyright Copyright (c) phpRack.com
 * @version $Id: index.js 584 2010-05-12 17:43:27Z yegor256@yahoo.com $
 * @author netcoderpl@gmail.com
 * @category phpRack
 */

$(
    function()
    {
        // List with DOM ids and test names
        var calls = phpParams.calls;

        String.prototype.stripTags = function ()
        {
            return this.replace(/<[^>]*>/g, '');
        };

        String.prototype.htmlspecialchars = function ()
        {
            return this.replace(/[<]/g, '&lt;').replace(/[>]/g, '&gt;');
        };

        var that = {
            focus: true,
            init: function()
            {
                $(window).blur(that.onBlur);
                $(window).focus(that.onFocus);
            },
            onBlur: function()
            {
                that.focus = false;
            },
            onFocus: function()
            {
                that.focus = true;
            },
            hasFocus: function()
            {
                return that.focus;
            }
        };
        phpRack_Window = that;
        phpRack_Window.init();

        // Our processing queue to control tests concurrency
        function phpRack_TaskQueue()
        {
            var that = {
                queue: [], // Used for storing waiting tasks
                threadsCount: 1, // How many concurent threads we can have
                activeThreadsCount: 0, // How many threads is currently running
                // Callback function executed after each task is finished
                onTaskFinish: function()
                {
                    that.activeThreadsCount--;
                    that._runNextTask();
                },
                // Add task to our processing queue
                add: function(task)
                {
                    that.queue.push(task);
                    that._runNextTask();
                },
                // Control tasks proccessing
                _runNextTask: function()
                {
                    // If we have free thread
                    if (that.activeThreadsCount < that.threadsCount) {
                        that.activeThreadsCount++;

                        // Get next task from queue and run it
                        if (that.queue.length) {
                            task = that.queue.shift();
                            // Set callback function on task, to know that we can execute next one
                            task.setOption('onFinish', that.onTaskFinish);
                            task.run();
                        }
                    }
                },
                // Set how many threads should be used for processing our task queue
                setThreadsCount: function(threadsCount)
                {
                    that.threadsCount = threadsCount;
                }
            };

            return that;
        }

        function phpRack_Timer(options)
        {
            var that = {
                elapsedSeconds: 0,
                // timer interval handler (used internally for unregister timer from window)
                intervalId: null,
                options: {onTick: null},
                // Constructor
                __construct: function(options)
                {
                    // Overwrite default options
                    $.extend(that.options, options);
                },
                // When timer is active this function will be automatically executed every second
                tick: function()
                {
                    that.elapsedSeconds++;

                    // If callback function was passed to constructor as param, execute it
                    if (that.options.onTick !== null) {
                        that.options.onTick(that);
                    }
                },
                start: function()
                {
                    // Stop timer if was earlier executed
                    that.stop();

                    // Reset timer elapsed time
                    that.elapsedSeconds = 0;

                    // Register timer function which will be called every 1000ms = 1s
                    that.intervalId = window.setInterval(that.tick, 1000);
                },
                stop: function()
                {
                    // If timer was earlier started and is still active
                    if (that.intervalId !== null) {
                        // Remove interval to stop calling tick() function every second
                        window.clearInterval(that.intervalId);
                    }
                    that.intervalId = null;
                },
                // Return elapsed seconds in format (m:ss)
                getFormattedTime: function()
                {
                    var result = '';
                    var minutes = Math.floor(that.elapsedSeconds / 60);
                    var seconds = that.elapsedSeconds % 60;
                    result += minutes + ':';
                    if (seconds < 10) {
                        result += '0';
                    }
                    result += seconds;
                    return result;
                },
                // Return elapsed seconds as integer
                getElapsedSeconds: function()
                {
                    return that.elapsedSeconds;
                }
            };

            that.__construct(options);
            return that;
        }

        function phpRack_Test(options)
        {
            // jQuery object which represent test task Element in DOM tree
            var $this = $(options.id);
            var that = {
                options: options,
                timer: null,
                isRunning: false,
                // jQuery object which represent test result Element in DOM tree
                $result: $this.find('span.result'),
                // jQuery object which represent test name Element in DOM tree
                $label: $this.find('span.label'),
                // jQuery object which represent tags Element in DOM tree
                $tags: $this.find('span.tags'),
                // jQuery object which represent test message Element in DOM tree
                $message: $this.find('pre'),
                displayTimer: false,
                timeoutId: null,
                // log lines buffer, used for control which lines should be still visible
                lines: [],
                // XMLHttpRequest object returned from $.ajax query,
                // give ability to abort connections
                xmlHttpRequest: null,
                // test execution time after which abort is possible
                abortWaitTime: 10,
                // Constructor
                __construct: function()
                {
                    // Create timer object and pass callback function to it
                    that.timer = new phpRack_Timer({onTick: that.onTimerTick});

                    // If user click on test name
                    that.$label.click(
                        function()
                        {
                            // Set that timer should be visible
                            that.displayTimer = true;

                            // Try execute test one more time
                            that.run();
                        }
                    );

                    // Attach one time event to "click to start..." text
                    if (!that.options.autoStart) {
                        that.$result.one('click', that.run);
                    }
                },
                setOption: function(key, value)
                {
                    that.options[key] = value;
                },
                onResultClick: function()
                {
                    // if test is running above that.abortWaitTime seconds abort it
                    if (that.isRunning && that.timer.getElapsedSeconds() >= that.abortWaitTime) {
                        that.xmlHttpRequest.abort();
                    } else {
                        // if test is finished, or running below than that.abortWaitTime seconds
                        if (that.$message.html()) {
                            that.$message.slideToggle();
                        }
                    }
                },
                _setStatus: function (success, message, options)
                {
                    // Update result span with OK/FAILURE depending on success param
                    if (success === true) {
                        that.$result.html(phpParams.ok).addClass('success');
                    } else {
                        that.$result.html(phpParams.failure).addClass('failure');
                    }

                    if (options && options.reload) {
                        that.$result.html(that.$result.html() + '?');
                    }

                    if (options && options.attachOutput) {
                        // Remove old execution time line
                        that.lines.pop();

                        // Remove empty line old execution time line
                        if (that.lines.length && that.lines[that.lines.length - 1].text === '') {
                            that.lines.pop();
                        }

                        // Remove lines after visibility expire time
                        while (that.lines.length > options.linesCount &&
                               that.lines[0].expireTime < (new Date()).getTime()) {
                            that.lines.shift();
                        }

                        // If new content begine with \n remove,
                        // because during lines joining we add this char
                        if (message.length && message[0] == "\n") {
                            message = message.substr(1);
                        }

                        // Add last log output to our lines set
                        $.each(
                            message.split("\n"),
                            function()
                            {
                                that.lines.push(
                                    {
                                        'text' : this,
                                        'expireTime' : (new Date()).getTime()  + options.secVisible * 1000
                                    }
                                );
                            }
                        );

                        // Create message from visible lines
                        message = '';
                        $.each(
                            that.lines,
                            function()
                            {
                                message += this.text + "\n";
                            }
                        );
                    }

                    if (options && options.tags) {
                        // cleanup tags list
                        that.$tags.empty();
                        $.each(
                            options.tags,
                            function(index, value)
                            {
                                // create new tag
                                var $tag = $('<span class="tag">' + value + '</span>');
                                $tag.click(
                                    function()
                                    {
                                        // set ajax call param and execute test again
                                        that.options.data['tag'] = value;
                                        that.run();
                                    }
                                );
                                // add tag to list
                                that.$tags.append($tag);
                            }
                        );
                    }

                    // Fill message pre tag with text returned from server
                    that.$message.html(message);

                    // Stop timer and update its state to user can repeat test
                    that.isRunning = false;
                    that.timer.stop();

                    // If we have registered callback function after test finish, execute it
                    if (that.options.onFinish) {
                        that.options.onFinish();
                    }
                },
                _startTimer: function()
                {
                    // Start test timer
                    that.timer.start();
                    that.onTimerTick();
                },
                // Callback function which will be called every seconds from phpRack_Timer
                onTimerTick: function()
                {
                    var elapsedSeconds = that.timer.getElapsedSeconds();
                    // If test is executed above 5s, set flag to display timer
                    if (elapsedSeconds > 5) {
                        that.displayTimer = true;
                    }

                    // Check that should display timer (User click or time execution > 5s)
                    if (that.displayTimer) {
                        var message = 'running&nbsp;(' + that.timer.getFormattedTime();
                        if (elapsedSeconds >= that.abortWaitTime) {
                            message += ',&nbsp;click&nbsp;to&nbsp;stop';
                        }
                        message += ')...';

                        that.$result.html(message);
                    }
                },
                _setReloadTimeout: function(seconds)
                {
                    that._removeReloadTimeout();

                    // if window has focus, and result message is expanded
                    if ((phpRack_Window.hasFocus() || !that.options.pauseWhenFocusLost) &&
                         that.$message.is(":visible")) {
                        var delay = seconds * 1000; // in miliseconds
                        // execute run() method with passed reload timeout
                        that.timeoutId = window.setTimeout(that.run, delay);
                    } else {
                        // if window has no focus, try again in 1 second
                        var sleepFunction = function()
                        {
                            that._setReloadTimeout(seconds);
                        };
                        window.setTimeout(sleepFunction, 1000);
                    }
                },
                _removeReloadTimeout: function()
                {
                    // If another run() is waiting for execute, ignore it
                    if (that.timeoutId) {
                        window.clearTimeout(that.timeoutId);
                    }
                },
                run: function()
                {
                    // Script still waiting for receive response from server
                    if (that.isRunning) {
                        // set flag to display timer
                        that.displayTimer = true;
                        that.onTimerTick();

                        // Prevent execution one more time because test is still running
                        return;
                    }

                    that._removeReloadTimeout();

                    // Set initial states
                    that._startTimer();
                    that.isRunning = true;
                    that.$result.html('running...');

                    // Remove earlier added handler to don't have duplicate
                    that.$result.unbind('click', that.onResultClick);
                    that.$result.bind('click', that.onResultClick);

                    // Remove added classes, because test can be executed many times
                    that.$result.removeClass('success failure');

                    // Make ajax query to server
                    that.xmlHttpRequest = $.ajax(
                        {
                            url: that.options.url,
                            data: that.options.data,
                            dataType: 'json',
                            cache: false, // Set flag to don't cache server response
                            // Standard callback if JSON returned from server is correct
                            // or have 0 bytes response
                            success: function (json)
                            {
                                // We should use standard display method without word wrap
                                that.$message.removeClass('word_wrap');

                                // If server returned no empty response
                                if (json) {
                                    // Set status to OK with log message
                                    that._setStatus(json.success, json.log.htmlspecialchars(), json.options);
                                    if (json.options) {
                                        if (json.options.reload) {
                                            that._setReloadTimeout(json.options.reload);
                                        }
                                        // Add request params which will need be send in next query
                                        if (json.options.data) {
                                            $.extend(that.options.data, json.options.data);
                                        }
                                    }
                                } else {
                                    // Set status to FAILURE with empty response message
                                    that._setStatus(false, 'Server returned empty response');
                                }
                            },
                            // Callback function if have some communication errors
                            error: function (XMLHttpRequest, textStatus, errorThrown)
                            {
                                // If we have some problem with server (Internal error, 404, 301/302 redirection)
                                if (XMLHttpRequest.status != 200) {
                                    // Create error message with headers
                                    message = XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText + "\n";
                                    message += XMLHttpRequest.responseText;
                                } else {
                                    // We received malformed JSON
                                    // (php script throwed some warning, unhandled exception, extra data, etc...)
                                    if (typeof errorThrown == 'object') {
                                        message = errorThrown.toString();
                                    } else {
                                        message = errorThrown;
                                    }
                                }

                                // Use word wrap for this messages type
                                that.$message.addClass('word_wrap');

                                // Set status to FAILURE with errorThrown as message
                                that._setStatus(false, message.htmlspecialchars());
                            }
                        }
                    );
                }
            };

            that.__construct();
            return that;
        }

        // Create queue for processing our tests
        var taskQueue = new phpRack_TaskQueue();

        // Set how many threads(tests) should be executed in same time
        // (not too high to avoid Apache limit problems)
        taskQueue.setThreadsCount(2);

        for (var id in calls) {
            var call = calls[id];
            // Create test object

            var data = {};
            data[phpParams.ajaxTag] = call.fileName;
            data[phpParams.ajaxToken] = call.divId;

            var test = new phpRack_Test(
                {
                    id: call.divId,
                    url: phpParams.requestUri,
                    data: data,
                    autoStart: call.autoStart,
                    pauseWhenFocusLost: call.pauseWhenFocusLost
                }
            );

            // Add test to processing queue if has autoStart enabled
            if (call.autoStart) {
                taskQueue.add(test);
            }
        }
    }
);
