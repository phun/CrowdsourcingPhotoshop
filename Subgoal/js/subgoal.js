
function Goal(name, taskList) {
    this.name = ko.observable(name);
    this.taskList = ko.observableArray(taskList);
}

function Task(name, parentList) {
    this.name = ko.observable(name);
    this.parentList = ko.observable(parentList);
}

var viewModel = {
    goalList: ko.observableArray([
        new Goal("goal 1", [
            new Task("Step 1. Add 1/2 tbsp of salt", "high"),
            new Task("Step 2. Add 3 tbsp of soy sauce", "high"),
            new Task("Step 3. Add 2 garlic pieces", "high")
        ]),
        new Goal("goal 2", [
            new Task("Step 4. Add oil to the pan", "normal"),
            new Task("Step 5. Fry for 5 minutes", "normal"),
            new Task("Step 6. Serve", "normal")
        ])
    ]),
    highPriorityTasks: ko.observableArray([
        new Task("Step 1. A", "high"),
        new Task("Step 2. B", "high"),
        new Task("Step 3. C", "high")
        ]),
    normalPriorityTasks: ko.observableArray([
        new Task("Fix fence", "normal"),
        new Task("Walk dog", "normal"),
        new Task("Read book", "normal")
        ]),
    selectedTask: ko.observable(),
    selectTask: function(task) {
        this.selectedTask(task);
    },
    selectedGoal: ko.observable(),
    selectGoal: function(goal) {
        this.selectedGoal(goal);
    },
    addGoal: function() {
        var goal = new Goal("new goal");
        this.selectedGoal(goal);
        this.goalList.push(goal);
    },
    removeGoal: function(goal) {
        // TODO: implement removal
    },
    trash: []
};

//connect items with observableArrays
ko.bindingHandlers.sortableList = {
    init: function(element, valueAccessor, allBindingsAccessor, context) {
        $(element).data("sortList", valueAccessor()); //attach meta-data
        $(element).sortable({
            update: function(event, ui) {
                var item = ui.item.data("sortItem");
                if (item) {
                    //identify parents
                    var originalParent = ui.item.data("parentList");
                    var newParent = ui.item.parent().data("sortList");
                    //figure out its new position
                    var position = ko.utils.arrayIndexOf(ui.item.parent().children(), ui.item[0]);
                    if (position >= 0) {
                        originalParent.remove(item);
                        newParent.splice(position, 0, item);
                    }
                    
                    ui.item.remove();
                }
            },
            connectWith: '.task-list'
        });
    }
};

//attach meta-data
ko.bindingHandlers.sortableItem = {
    init: function(element, valueAccessor) {
        var options = valueAccessor();
        $(element).data("sortItem", options.item);
        $(element).data("parentList", options.parentList);
    }
};

//control visibility, give element focus, and select the contents (in order)
ko.bindingHandlers.visibleAndSelect = {
    update: function(element, valueAccessor) {
        ko.bindingHandlers.visible.update(element, valueAccessor);
        if (valueAccessor()) {
            setTimeout(function() {
                $(element).focus().select();
            }, 0); //new tasks are not in DOM yet
        }
    }
}

ko.applyBindings(viewModel);

