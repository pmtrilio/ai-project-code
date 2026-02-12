                                } else {
                                    elt.innerHTML = '<span>▶</span>';
                                    elt.className = 'sf-dump-ref';
                                }
                                elt.className += ' sf-dump-toggle';
                            } catch (e) {
                                if ('&' == elt.innerHTML.charAt(0)) {
                                    elt.innerHTML = '…';
                                    elt.className = 'sf-dump-ref';
                                }
                            }
                        }
                    }
                }

                if (doc.evaluate && Array.from && root.children.length > 1) {
                    root.setAttribute('tabindex', 0);

                    SearchState = function () {
                        this.nodes = [];
                        this.idx = 0;
                    };
                    SearchState.prototype = {
                        next: function () {
                            if (this.isEmpty()) {
                                return this.current();
                            }
                            this.idx = this.idx < (this.nodes.length - 1) ? this.idx + 1 : 0;

                            return this.current();
                        },
                        previous: function () {
                            if (this.isEmpty()) {
                                return this.current();
                            }
                            this.idx = this.idx > 0 ? this.idx - 1 : (this.nodes.length - 1);

                            return this.current();
                        },
                        isEmpty: function () {
                            return 0 === this.count();
                        },
                        current: function () {
                            if (this.isEmpty()) {
                                return null;
                            }
                            return this.nodes[this.idx];
                        },
                        reset: function () {
                            this.nodes = [];
                            this.idx = 0;
                        },
                        count: function () {
                            return this.nodes.length;
                        },
                    };

                    function showCurrent(state)
                    {
                        var currentNode = state.current(), currentRect, searchRect;
                        if (currentNode) {
                            reveal(currentNode);
                            highlight(root, currentNode, state.nodes);
                            if ('scrollIntoView' in currentNode) {
                                currentNode.scrollIntoView(true);
                                currentRect = currentNode.getBoundingClientRect();
                                searchRect = search.getBoundingClientRect();