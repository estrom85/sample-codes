#include "PackagerForm.h"

PackagerForm::PackagerForm(){
    widget.setupUi(this);
    model = new ViewModel();
    dialog = new PreviewDialog(this);
    
    this->setWindowTitle("Package Manager");
    widget.tableView->setSelectionBehavior(QAbstractItemView::SelectRows);
    widget.tableView->setModel(model);
    widget.tableView->horizontalHeader()->setResizeMode(QHeaderView::Stretch);
    setButtons();
    
    //connect
    connect(widget.newBttn,SIGNAL(clicked()),model,SLOT(CreatePackage()));
    connect(model,SIGNAL(PackageChanged(Package*)),this,SLOT(packageLoaded(Package*)));
    connect(widget.loadBttn,SIGNAL(clicked()),model,SLOT(LoadPackage()));
    connect(widget.tableView->selectionModel(),SIGNAL(selectionChanged(const QItemSelection&, const QItemSelection&)),
            this,SLOT(selectionChanged(const QItemSelection&, const QItemSelection&)));
    connect(widget.saveBttn,SIGNAL(clicked()),model,SLOT(SavePackage()));
    connect(widget.addBttn,SIGNAL(clicked()),model,SLOT(AddFiles()));
    connect(widget.expBttn,SIGNAL(clicked()),model,SLOT(ExportFiles()));
    connect(widget.remBttn,SIGNAL(clicked()),model,SLOT(RemoveFiles()));
    connect(widget.chngBttn,SIGNAL(clicked()),model,SLOT(EditKey()));
    connect(widget.closeBttn,SIGNAL(clicked()),this,SLOT(closeApp()));
    connect(widget.prevBttn,SIGNAL(clicked()),dialog,SLOT(ShowDialog()));
    connect(model,SIGNAL(PackageChanged(Package*)),dialog,SLOT(ChangePackage(Package*)));
    connect(widget.tableView,SIGNAL(doubleClicked(const QModelIndex&)),dialog,
            SLOT(ShowDialog(const QModelIndex&)));
}
PackagerForm::~PackagerForm(){
    dialog->close();
    delete model;
    delete dialog;
}

/*Slots*/
void PackagerForm::closeApp(){
    
    close();
}

void PackagerForm::packageLoaded(Package* package){
    this->setWindowTitle(QString("Package Manager - ") + QString(package->getFileName().c_str()));
    setButtons();
}

void PackagerForm::selectionChanged(const QItemSelection& selected, const QItemSelection& deselected){
    model->setSelectedItems(this->widget.tableView->selectionModel()->selectedRows(0));
}

void PackagerForm::setButtons(){
    bool enabled = model->packageLoaded();
    widget.saveBttn->setEnabled(enabled);
    widget.addBttn->setEnabled(enabled);
    widget.expBttn->setEnabled(enabled);
    widget.remBttn->setEnabled(enabled);
    widget.chngBttn->setEnabled(enabled);
    widget.prevBttn->setEnabled(enabled);
}

void PackagerForm::setPreviewDialog(){
    
}
