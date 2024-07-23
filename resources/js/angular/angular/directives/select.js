app.directive("select", [
    "$timeout",
    function ($timeout) {
        return {
            restrict: "E",
            priority: 1000,
            scope: {
                ngModel: "=",
                ngChange: "&",
                drop: "@",
                options: "=",
                customize: "=",
                disabled: '<',
                ngDisabled: '<',
                mask: "@",
            },
            link: function (scope, element, attrs) {
                const isMultiple = element.attr("multiple");
                if (element[0].tagName !== "SELECT" || element.is(".ignore")) {
                    return;
                }
                // scope.drop = scope.drop || "down";

                let placeholder =
                    element.attr("placeholder") ||
                    (isMultiple ? "Select Options" : "Select Option");
                const label = element.prev("label:not(.ignore)");
                if (label.length > 0) {
                    placeholder = label.text();
                    label.remove();
                }

                let selectedValues = [];
                let selectedValue = "";
                const selections = element.find("option[selected]");

                selections.each(function () {
                    const value = $(this).val();
                    const text = $(this).text();
                    if (isMultiple) {
                        selectedValues.push(value);
                    } else {
                        selectedValue = value;
                    }
                });

                

                const customContainer = angular.element(
                    `<span class="dropdown-container group">
                    <input type="hidden" class="hidden-input" ng-model="${scope.ngModel}"/>
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle relative input text-sm">
                            <span class="dropdown-toggle-text">${placeholder||'Select'}</span>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu"></ul>
                    </div>
                    <div class="dd-backdrop"></div>
                </span>`
                );

                const trigger = customContainer.find(".dropdown-toggle");
                const dropdown = customContainer.find(".dropdown");
                const optionsList = dropdown.find(".dropdown-menu");
                const input = customContainer.find(".hidden-input");
                const backdrop = customContainer.find(".dd-backdrop");
                const triggerText = trigger.find(".dropdown-toggle-text");

                if (placeholder) {
                    customContainer
                        .find(".dropdown-menu")
                        .append(
                            `<li class="dropdown-header">${placeholder}</li>`
                        );
                }
                let display = placeholder;

                const updateTriggerText = () => {
                    let text = null;

                    if (isMultiple && scope.ngModel.length > 0) {
                        let cut = scope.ngModel.slice(0, 2);
                        let others = scope.ngModel.length - cut.length;

                        text = cut
                            .map(
                                (value) =>
                                    `<span class="chip" style="max-width:40px;text-overflow:ellipsis; overflow:hidden" data-value="${value}">${scope.items[value]}</span>`
                            )
                            .join("");

                        if (others > 0) {
                            text += `<span class="chip" style="max-width:40px;text-overflow:ellipsis; overflow:hidden">+${others}</span>`;
                        }
                    } else if (!isMultiple && scope.ngModel) {
                        text = scope.items[scope.ngModel];
                    }
                  
                  if (text) {
                        triggerText.html(text);
                        trigger.attr(
                            "aria-expanded",
                            customContainer.hasClass("show")
                        );
                    }
                }
                


                if (isMultiple && selectedValues.length > 0) {
                    const mapValues = selectedValues.map((value) => {
                        return `<span class="chip" style="max-width:40px;text-overflow:ellipsis; overflow:hidden" data-value="${value}">${scope.items[value]}</span>`;
                    });
                    let cut = mapValues.slice(0, 2);
                    let others = mapValues.length - cut.length;
                    if (others > 0) {
                        cut.push(
                            `<span class="chip" style="max-width:40px;text-overflow:ellipsis; overflow:hidden">+${others}</span>`
                        );
                    }
                    display = cut.join("");
                }
                

                const insertInput = () => {
                    const input = angular.element("<input>");
                    input
                        .attr({
                            type: "text",
                            placeholder: placeholder
                                ? placeholder.replace("Select", "Enter")
                                : "Enter value",
                        })
                        .addClass("input");

                    customContainer.replaceWith(input);

                    if (scope.mask) {
                        input.mask(scope.mask);
                    }

                    input.on("keyup", function (e) {
                        scope.ngModel = e.target.value;
                        scope.$apply();

                        scope.ngChange.call(scope);
                    });
                };

                const clickHandler = function () {
                    if (isMultiple) {
                        customContainer.on(
                            "click",
                            'input[type="checkbox"]',
                            function () {
                                const value = $(this).val();
                                const index = scope.ngModel.indexOf(value);

                                if ($(this).prop("checked")) {
                                    if (index === -1) {
                                        scope.ngModel.push(value);
                                    }
                                } else {
                                    if (index !== -1) {
                                        scope.ngModel.splice(index, 1);
                                    }
                                }

                                updateTriggerText();
                                input.val(scope.ngModel);
                                scope.$apply();
                                scope.ngChange.call(scope);
                            }
                        );
                    } else {
                        customContainer.on(
                            "click",
                            ".dropdown-item[data-value]",
                            function () {
                                if ($(this).is(".drop-into-input")) {
                                    insertInput();
                                } else {
                                    const value = $(this).data("value");
                                    if (value != scope.ngModel) {
                                        scope.ngModel = value;
                                        updateTriggerText();
                                        optionsList
                                            .find(".dropdown-item.selected")
                                            .removeClass("selected");
                                        $(this).addClass("selected");
                                    }

                                    customContainer.removeClass("show");
                                    input.val(scope.ngModel);
                                    scope.$apply();
                                    scope.ngChange.call(scope);
                                }
                            }
                        );
                    }

                    
                };

                const prepareItems = () => {
                    let items = {};

                    optionsList.empty();

                    if (placeholder) {
                        optionsList.append(
                            `<li class="dropdown-header">${placeholder}</li>`
                        );
                    }

                    if (Array.isArray(scope.options)) {
                        scope.options.forEach((item) => {
                            items[item] = item;
                        });
                    } else if (
                        typeof scope.options === "object" &&
                        scope.options !== null
                    ) {
                        items = scope.options;
                    } else {
                        return;
                    }

                    scope.items = items;

                    Object.keys(items).forEach((key) => {
                        let listItem = `<li class="dropdown-item" data-value="${key}">${items[key]}</li>`;

                        if (isMultiple) {
                            listItem = `<li class="dropdown-item">
                                      <label class="flex items-center gap-1.5">
                                          <span style="width:20px">
                                            <input type="checkbox" class="checkbox" value="${key}" ng-model="ngModel.indexOf(${key}) !== -1">
                                           </span>
                                           <span>${items[key]}</span>
                                       </label>
                                      </li>`;
                        }

                        const listItemElement = angular.element(listItem);
                        optionsList.append(listItemElement);
                    });
                    if (scope.customize) {
                        optionsList.append(
                            '<li class="dropdown-item drop-into-input" data-value="custom">Customize</li>'
                        );
                    }
                    updateTriggerText();

                    clickHandler();
                };

                const initializeItems = () => {
                    scope.items = {};
                    if (isMultiple && scope.ngModel) {
                        scope.ngModel = [];
                    }
                    element.find("option").each(function () {
                        const value = $(this).val();
                        scope.items[value] = $(this).text();

                        let item = `<li class="dropdown-item ${
                            !value ? "disabled" : ""
                        } ${
                            value && selectedValue === value ? "selected" : ""
                        }" data-value="${value}">${$(this).text()}</li>`;

                        if (value && scope.ngModel && selectedValue === value) {
                            scope.ngModel = value;
                        }
                        if (isMultiple) {
                            item = `<li class="dropdown-item ${
                                !value ? "disabled" : ""
                            }">
                                  <label class="flex items-center gap-1.5">
                                      <span style="width:20px">
                                        <input type="checkbox" class="checkbox" value="${value}" ng-model="ngModel.indexOf(${value}) !== -1" ${
                                selectedValues.includes(value) ? "checked" : ""
                            }>
                                      </span>
                                      <span>${$(this).text()}</span>
                                  </label>
                                </li>`;
                            if (
                                scope.ngModel &&
                                selectedValues.includes(value)
                            ) {
                                scope.ngModel.push(value);
                            }
                        }

                        const listItemElement = angular.element(item);
                        customContainer
                            .find(".dropdown-menu")
                            .append(listItemElement);
                    });

                    clickHandler();
                };

                if (scope.options) {
                    prepareItems();
                } else {
                    initializeItems();
                }

                scope.ngModel = isMultiple ? selectedValues : selectedValue;
                element.replaceWith(customContainer);

                if (scope.drop) {
                    dropdown.addClass("drop-" + scope.drop);
                }
                trigger.on("click", function () {
                    customContainer.toggleClass("show");
                    $(this).attr(
                        "aria-expanded",
                        customContainer.hasClass("show")
                    );
                    adjustDropdownPosition();
                });

                optionsList.attr("tabindex", 0);
                trigger.attr("aria-disabled", trigger.is(":disabled"));

                backdrop.on("click", function () {
                    customContainer.removeClass("show");
                });

                function adjustDropdownPosition() {
                    if (scope.drop || !customContainer.hasClass("show")) return;

                    let containerRect = trigger
                        .parent()[0]
                        .getBoundingClientRect();
                    let innerHeight = window.innerHeight;
                    if (scope.relative) {
                        innerHeight = angular.element(scope.relative)[0].innerHeight;
                    }
                    const listRect = optionsList[0].getBoundingClientRect();
                    const availableSpace =
                        innerHeight - containerRect.bottom;

                    if (listRect.height > availableSpace) {
                        optionsList.css(
                            "top",
                            `-${listRect.height - availableSpace}px`
                        );
                    } else {
                        optionsList.css("top", null);
                    }
                }

                scope.$watch("ngModel", function (newValue) {
                    
                    if (newValue) {
                        if (scope.options) {
                            prepareItems();
                        } else {
                            
                            const items =
                                customContainer.find(".dropdown-item");
                            items.each(function () {
                                const selection = $(this).data("value") === newValue;

                                $(this).toggleClass("selected", selection);

                                if (selection) {
                                   triggerText.text($(this).text());
                                }
                            });
                        }
                    }
                    updateTriggerText();
                });

                scope.$watch("options", function () {
                    if (scope.options) {
                        prepareItems();
                    }
                });

                scope.$watch(
                    function () {
                        return element.find("option").length;
                    },
                    function () {
                        $timeout(function () {
                            
                            //initializeItems();
                        });
                    }
                );

                scope.$watch('ngDisabled', function(newValue, oldValue) {
                    trigger.prop('disabled', newValue);
                });

                scope.$watch('disabled', function(newValue, oldValue) {
                    trigger.prop('disabled', newValue);
                });

                scope.$watch(
                    function () {
                        return element.children().length;
                    },
                    function () {
                        $timeout(function () {
                            
                            // initializeItems();
                        });
                    }
                );

                updateTriggerText();

                window.addEventListener("resize", adjustDropdownPosition);

                element.on("$destroy", function () {
                    window.removeEventListener(
                        "resize",
                        adjustDropdownPosition
                    );
                });
            },
        };
    },
]);

app.directive("chipSelect", function () {
    return {
        restrict: "E",
        scope: {
            items: "=",
            id: "@",
            label: "@",
            class: "@",
            ngModel: "=",
        },
        controller: "RootController",
        templateUrl: "/components/chip-select.html",
        link: function (scope, element, attrs) {
            scope.class = scope.class || "grid gap-2";
            scope.id = scope.id || "id";
            scope.label = scope.label || scope.id || "name";

            if (!Array.isArray(scope.ngModel)) {
                scope.ngModel = [];
            }

            scope.index = (item) => {
                if (!item) {return -1;}

                return scope.get_index(scope.ngModel, item[scope.id], scope. id);
            }
 
            // scope.selected = (item) => {
            //     return scope.index(item) >= 0;
            // };

            scope.toggle = (item) => {
                const index = scope.index(item);

                if (index >= 0) {
                    scope.ngModel.splice(index, 1);
                } else {
                    scope.ngModel.push(item);
                }
                scope.$apply();
            };
        },
    };
});
