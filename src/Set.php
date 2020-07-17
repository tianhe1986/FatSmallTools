<?php
namespace FatSmallTools;

class Color
{
    const RED = 1;
    const BLACK = 2;
}

// tree node
class TreeNode
{
    public $data;
    public $color;
    public $left; // left son
    public $right; // right son
    public $parent;
    
    public function __construct($data) {
        $this->data = $data;
        $this->color = Color::RED;
        $this->left = $this->right = $this->parent = null;
    }
    
    public function isOnLeft()
    {
        return $this == $this->parent->left;
    }
    
    public function sibling()
    {
        if ($this->parent == null) { // no parent, no sibling
            return null;
        }
        
        if ($this->isOnLeft()) {
            return $this->parent->right;
        }
        
        return $this->parent->left;
    }
    
    public function hasRedChild()
    {
        return ($this->left != null && $this->left->color == Color::RED) || ($this->right != null && $this->right->color == Color::RED);
    }
}

class RedBlackTree
{
    private $root;
    
    private $size;
    
    protected $compareFun; // compare function
    
    public function __construct(callable $compareFun = null) {
        $this->root = null;
        $this->size = 0;
        $this->compareFun = $compareFun;
    }
    
    public function orderPrint()
    {
        $this->innerOrderPrint($this->root);
    }
    
    public function levelPrint()
    {
        $this->innerLevelPrint($this->root);
    }
    
    // for debug
    protected function innerOrderPrint(TreeNode $root = null)
    {
        if ($root === null) {
            return;
        }
        
        $this->innerOrderPrint($root->left);
        echo $root->data . "\n";
        $this->innerOrderPrint($root->right);
    }
    
    // for debug
    protected function innerLevelPrint(TreeNode $root = null)
    {
        if ($root === null) {
            return;
        }
        
        $queue = new \SplQueue();
        $queue->enqueue([$root, 0]);
        
        while ( ! $queue->isEmpty()) {
            $item = $queue->dequeue();
            
            $temp = $item[0];
            echo "level ".$item[1].", color ".$temp->color.", data".$temp->data."\n";
            
            if ($temp->left !== null) {
                $queue->enqueue([$temp->left, $item[1] + 1]);
            }
            
            if ($temp->right !== null) {
                $queue->enqueue([$temp->right, $item[1] + 1]);
            }
        }
    }
    
    protected function compare(TreeNode $nodea, TreeNode $nodeb)
    {
        if ($this->compareFun !== null) {
            return $this->compareFun($nodea->data, $nodeb->data);
        }
        
        // treat as number
        return $nodea->data - $nodeb->data;
    }


    protected function bstInsert(TreeNode $root = null, TreeNode $node = null)
    {
        // empty, then the new node become new root
        
        if (null === $root) {
            $this->size++;
            return $node;
        }
        
        if (null === $node) {
            return $root;
        }
        
        $compareResult = $this->compare($node, $root);
        
        if ($compareResult < 0 ) { // node is small, insert into left son tree
            $root->left = $this->bstInsert($root->left, $node);
            $root->left->parent = $root;
        } else if ($compareResult > 0) { // node is bigger, insert into right son tree
            $root->right = $this->bstInsert($root->right, $node);
            $root->right->parent = $root;
        }
        
        return $root;
    }
    
    protected function rotateLeft(TreeNode $node)
    {
        // right son, to be new root of this sub tree
        $rightSon = $node->right;
        
        $node->right = $rightSon->left;
        $node->right->parent = $node;
        
        // deal parent of node
        $parent = $node->parent;
        if ($parent === null) { // no parent , must be root
            $this->root = $rightSon;
        } else if ($node == $parent->left) {
            $parent->left = $rightSon;
        } else {
            $parent->right = $rightSon;
        }
        
        $rightSon->parent = $parent;
        
        $node->parent = $rightSon;
        $rightSon->left = $node;
    }
    
    protected function rotateRight(TreeNode $node)
    {
        // similar deal as rotateLeft
        $leftSon = $node->left;
        
        $node->left = $leftSon->right;
        $node->left->parent = $node;
        
        $parent = $node->parent;
        if ($parent === null) {
            $this->root = $leftSon;
        } else if ($node == $parent->left) {
            $parent->left = $leftSon;
        } else {
            $parent->right = $leftSon;
        }
        
        $leftSon->parent = $parent;
        
        $node->parent = $leftSon;
        $leftSon->right = $node;
    }
    
    // fixing double red
    protected function fixViolation(TreeNode $root, TreeNode $node)
    {
        // reference https://www.geeksforgeeks.org/red-black-tree-set-2-insert/
        
        while ($node != $root && $node->color == Color::RED && $node->parent->color == Color::RED) {
            $parentNode = $node->parent;
            $grandParentNode = $parentNode->parent;
            
            // parent is left child of grand-parent
            if ($parentNode == $grandParentNode->left) {
                $uncleNode = $grandParentNode->right;
                
                // uncle is also red， then grand-parent must be black
                // recoloring parent and uncle to black, grand-parent to red, continue fixing grand-parent
                if ($uncleNode !== null && $uncleNode->color == Color::RED) {
                    $uncleNode->color = Color::BLACK;
                    $parentNode->color = Color::BLACK;
                    $grandParentNode->color = Color::RED;
                    $node = $grandParentNode;
                } else {
                    // Left Right Case, left rotate parent, become Left Left Case
                    if ($node == $parentNode->right) {
                        $this->rotateLeft($parentNode);
                        $node = $parentNode;
                        $parentNode= $node->parent;
                    }
                    
                    // Left Left Case, right rotate grand-parent, then swap the color of parent and grand-parent
                    $this->rotateRight($grandParentNode);
                    $temp = $parentNode->color;
                    $parentNode->color = $grandParentNode->color;
                    $grandParentNode->color = $temp;
                    
                    $node = $parentNode;
                    // TODO: always break since parent must be BLACK ？
                }
            } else { // parent is right child of grand-parent
                $uncleNode = $grandParentNode->left;
                
                // uncle is also red
                if ($uncleNode !== null && $uncleNode->color == Color::RED) {
                    $uncleNode->color = Color::BLACK;
                    $parentNode->color = Color::BLACK;
                    $grandParentNode->color = Color::RED;
                    $node = $grandParentNode;
                } else {
                    // RIGHT LEFT case, right rotate parent, become RIGHT RIGHT case
                    if ($node == $parentNode->left) {
                        $this->rotateRight($parentNode);
                        $node = $parentNode;
                        $parentNode = $node->parent;
                    }
                    
                    // RIGHT RIGHT case, left rotate grand-parent, then swap the color of parent and grand-parent
                    $this->rotateLeft($grandParentNode);
                    
                    $temp = $parentNode->color;
                    $parentNode->color = $grandParentNode->color;
                    $grandParentNode->color = $temp;
                    
                    $node = $parentNode;
                }
            }
        }
        
        // always set root to BLACK
        $this->root->color = Color::BLACK;
    }
    
    public function insert($data)
    {
        $node = new TreeNode($data);
        
        // Do normal BST insert
        $this->root = $this->bstInsert($this->root, $node);
        
        // not insert
        if ($node->parent === null && $node != $this->root) {
            return;
        }

        // Fix Red Black Tree violations
        $this->fixViolation($this->root, $node);
    }
    
    // find node that do not have a left child in the subtree of the given node
    // that is, the minimum value node of the subtree
    private function successor(TreeNode $x)
    { 
        $temp = $x; 

        while ($temp->left != null){
            $temp = $temp->left;
        }

        return $temp; 
    } 
    
    // find node that replaces a deleted node in BST 
    private function bstReplace(TreeNode $x)
    { 
        // when node have 2 children 
        if ($x->left != null && $x->right != null){
            return $this->successor($x->right);
        }

        // when leaf 
        if ($x->left == null && $x->right == null){
            return null;
        }

        // when single child 
        if ($x->left != null) {
            return $x->left;
        } else {
            return $x->right;
        }
    } 
    
    private function deleteNode(TreeNode $v)
    {
        // find the replace node
        $u = $this->bstReplace($v);
        
        // True when u and v are both black
        $uvBlack = (($u == null || $u->color == Color::BLACK) && $v->color = Color::BLACK);
        $parent = $v->parent;
        
        if ($u == null) { // u is NULL therefore v is leaf 
            if ($v == $this->root) { // Oh root
                $this->root = null;
            } else {
                if ($uvBlack) {
                    // u and v both black
                    // v is leaf, fix double black at v
                    $this->fixDoubleBlack($v);
                } else {
                    // v is red, need do nothing
                }
                
                // delete v from the tree
                if ($v->isOnLeft()) {
                    $parent->left = null;
                } else {
                    $parent->right = null;
                }
            }
            
            unset($v);
            return;
        }
        
        if ($v->left == null || $v->right == null) { // v has only one child
            if ($v == $this->root) { // v is root， and u is the only child， then u will be the new root
                $u->parent = null;
                $this->root = $u;
                unset($v);
            } else {
                // Detach v from tree and move u up
                if ($v->isOnLeft()) {
                    $parent->left = $u;
                } else {
                    $parent->right = $u;
                }
                $u->parent = $parent;
                unset($v);
                
                // if u and v both black, fix double black at u
                if ($uvBlack) {
                    $this->fixDoubleBlack($u);
                } else { // u or v is red, just set u black
                    $u->color = Color::BLACK;
                }
            }
            
            return;
        }
        
        // v has 2 children, then swap the value of u and v, recurse delete u
        $this->swapValues($v, $u);
        $this->deleteNode($u);
    }
    
    private function fixDoubleBlack(TreeNode $x)
    {
        // reach root
        if ($x == $this->root) {
            return;
        }
        
        // double black + red = black
        if ($x->color == Color::RED) {
            $x->color = Color::BLACK;
            return;
        }
        
        $sibling = $x->sibling();
        $parent = $x->parent;
        
        if ($sibling == null) { // no sibling, push up
            $this->fixDoubleBlack($parent);
        } else {
            if ($sibling->color == Color::RED) { // sibling red, recolor sibling to black, parent to red, and then rotate, continue fixDoubleBlack
                $parent->color = Color::RED;
                $sibling->color = Color::BLACK;
                
                if ($x->isOnLeft()) {
                    $this->rotateLeft($parent);
                } else {
                    $this->rotateRight($parent);
                }
                
                $this->fixDoubleBlack($x);
            } else { // sibling black
                if ($sibling->hasRedChild()) { // sibling has red child
                    if ( ! $sibling->isOnLeft()) { // sibling is right child
                        if ($sibling->right != null && $sibling->right->color == Color::RED) { // red child is right child
                            // recolor sibling right child to black
                            $sibling->right->color = Color::BLACK;
                            // trick, recolor sibling to parent's color, and then always set parent to black
                            $sibling->color = $parent->color;
                            
                            // left rotate parent
                            $this->rotateLeft($parent);
                        } else { // red child is left child
                            // trick, recolor sibling left child to parent's color, and then always set parent to black
                            $sibling->left->color = $parent->color;
                            
                            // right rotate sibling
                            $this->rotateRight($sibling);
                            
                            // left rotate parent
                            $this->rotateLeft($parent);
                        }
                    } else { // sibling is left child, mirror
                        if ($sibling->left != null && $sibling->left->color == Color::RED) {
                            $sibling->left->color = Color::BLACK;
                            $sibling->color = $parent->color;
                            $this->rotateRight($parent);
                        } else {
                            $sibling->right->color = $parent->color;
                            $this->rotateLeft($sibling);
                            $this->rotateRight($parent);
                        }
                    }
                    
                    // since always set parent to black, do it common
                    $parent->color = Color::BLACK;
                } else { // 2 black children recolor sibling to red, push up double black to parent
                    $sibling->color = Color::RED;
                    $this->fixDoubleBlack($parent);
                }
            }
        }
    }
    
    private function swapValues(TreeNode $v, TreeNode $u)
    {
        $temp = $v->data;
        $v->data = $u->data;
        $u->data = $temp;
    }
    
    private function search($data)
    {
        $temp = $this->root;
        
        while ($temp != null) {
            $compareResult = $this->compareFun($data, $temp->data);
            
            if ($compareResult == 0) {
                break;
            } else if ($compareResult < 0) {
                if ($temp->left == null) {
                    break;
                } else {
                    $temp = $temp->left;
                }
            } else {
                if ($temp->right == null) {
                    break;
                } else {
                    $temp = $temp->right;
                }
            }
        }
        
        return $temp;
    }
    
    public function erase($data)
    {
        // Searching the node and delete
        if ($this->root == null) {
            // Tree is empty 
            return;
        }
        
        $v = $this->search($data);
        
        if ($this->compareFun($v->data, $data) != 0) {
            return;
        }
        
        $this->deleteNode($v);
    }
    
    public function find($data)
    {
        // Call real search
        $node = $this->search($data);
        
        if ($this->compareFun($node->data, $data) == 0) {
            return $node;
        }
        
        return null;
    }
}
