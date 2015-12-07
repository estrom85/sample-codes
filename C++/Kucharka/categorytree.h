#ifndef CATEGORYTREE_H
#define CATEGORYTREE_H

#include <QAbstractItemModel>
#include <item.h>
#include <QFont>

class CategoryTree : public QAbstractItemModel
{
    Q_OBJECT
protected:
    Item* root;

protected:
    virtual QVariant getItemData(const QModelIndex &index) const;
    virtual QVariant getItemFont(const QModelIndex &index) const;
    virtual QVariant getItemIcon(const QModelIndex &index) const;
    virtual QVariant getItemBackground(const QModelIndex &index) const;
    virtual QVariant getItemForeground(const QModelIndex &index) const;

public:
    explicit CategoryTree(Item* root,QObject *parent = 0);
    virtual ~CategoryTree();

    virtual QVariant data(const QModelIndex &index, int role) const;
    virtual Qt::ItemFlags flags(const QModelIndex &index) const;
    virtual QVariant headerData(int section, Qt::Orientation orientation,
                             int role = Qt::DisplayRole) const;
    virtual QModelIndex index(int row, int column,
                           const QModelIndex &parent = QModelIndex()) const;
    virtual QModelIndex parent(const QModelIndex &index) const;
    virtual int rowCount(const QModelIndex &parent = QModelIndex()) const;
    virtual int columnCount(const QModelIndex &parent = QModelIndex()) const;
    
signals:
    
public slots:
    
};

#endif // CATEGORYTREE_H
