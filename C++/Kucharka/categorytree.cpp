#include "categorytree.h"


CategoryTree::CategoryTree(Item* root,QObject *parent) :
    QAbstractItemModel(parent)
{
    this->root=root;
}

CategoryTree::~CategoryTree(){
    //qDebug()<<"Tree model destroyed";
}

QVariant CategoryTree::data(const QModelIndex &index, int role) const{
    switch(role){
    case Qt::DisplayRole:
        return getItemData(index);
    case Qt::FontRole:
        return getItemFont(index);
    case Qt::BackgroundRole:
        return getItemBackground(index);
    case Qt::DecorationRole:
        return getItemIcon(index);
    }
    return QVariant();



    /*
    if(!index.isValid())
        return QVariant();
    if(role!=Qt::DisplayRole)
        return QVariant();


    Item* item=static_cast<Item*>(index.internalPointer());

    return QVariant(item->getName());
    */
}

Qt::ItemFlags CategoryTree::flags(const QModelIndex &index) const{
    if(!index.isValid())
        return 0;
    Item* item;
    item=static_cast<Item*>(index.internalPointer());
    Qt::ItemFlags flags=Qt::ItemIsEnabled;
    if(!item->isCategory())
        flags=flags|Qt::ItemIsSelectable;

    return flags;
}

QVariant CategoryTree::headerData(int section, Qt::Orientation orientation,
                         int role) const{
    if (orientation == Qt::Horizontal && role == Qt::DisplayRole)
        return QVariant(QString::fromUtf8("Vyber recept"));
    return QVariant();
}

QModelIndex CategoryTree::index(int row, int column,
                       const QModelIndex &parent) const{
    if (!hasIndex(row, column, parent))
             return QModelIndex();

    Item *parentItem;

    if (!parent.isValid())
        parentItem = root;
    else
        parentItem = static_cast<Item*>(parent.internalPointer());

    Item *childItem = parentItem->getChild(row);
    if (childItem)
        return createIndex(row, column, childItem);
    else
        return QModelIndex();
}

QModelIndex CategoryTree::parent(const QModelIndex &index) const{
    if(!index.isValid())
        return QModelIndex();

    Item* child=static_cast<Item*>(index.internalPointer());
    Item* parent=child->getParent();

    if(parent==root)
        return QModelIndex();
    return createIndex(parent->getRow(),0,parent);
}

int CategoryTree::rowCount(const QModelIndex &parent) const{
    Item* parentItem;
    if(parent.column()>0)
        return 0;

    if(!parent.isValid())
        parentItem=root;
    else
        parentItem=static_cast<Item*>(parent.internalPointer());

    return parentItem->getNumRows();
}

int CategoryTree::columnCount(const QModelIndex &parent) const{
    return 1;
}



QVariant CategoryTree::getItemData(const QModelIndex &index) const
{
    if(!index.isValid())
        return QVariant();

    Item* item=static_cast<Item*>(index.internalPointer());
    //qDebug()<<"item "<<item->getName()<<" displayed";
    return QVariant(item->getName());
}

QVariant CategoryTree::getItemFont(const QModelIndex &index) const
{
    if(!index.isValid())
        return QVariant();

    Item* item=static_cast<Item*>(index.internalPointer());

    if(item->isCategory())
        return QVariant();

    QFont font;

    font.setFamily("Comic Sans MS");
    //font.setBold(true);
    font.setItalic(true);
    //font.setPointSize(12);
    return QVariant(font);
}

QVariant CategoryTree::getItemIcon(const QModelIndex &index) const
{
    return QVariant();
}

QVariant CategoryTree::getItemBackground(const QModelIndex &index) const
{
    return QVariant();
}

QVariant CategoryTree::getItemForeground(const QModelIndex &index) const
{
    return QVariant();
}


